<?php

class __AsyncMessageFactory {
    
    static private $_instance = null;
    
    private function __construct() {
        
    }
    
    /**
     * Gets a {@link __AsyncMessageFactory} singleton instance
     *
     * @return __AsyncMessageFactory
     */
    static public function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __AsyncMessageFactory();
        }
        return self::$_instance;
    }

    public function createComponentsAsyncMessage(array &$components, $startup_message = false) {
        //create async message:
        $return_value = new __AsyncMessage();
        $commands = array();
        foreach($components as &$component) {
            if($component instanceof __IComponent) {
                if($startup_message) {
                    $commands = array_merge($commands, $this->_getComponentSetupCommands($component));
                }
                else {
                    $commands = array_merge($commands, $this->_getComponentCommands($component));
                }                
            }
        }
        $return_value->setCommands($commands);
        return $return_value;
    }
    
    public function createProgressAsyncMessage(__IComponent &$component) {
        $command = new __AsyncMessageCommand();
        $command->setClass('__UpdateProgressIndicatorCommand');
        $command->setData(array('code' => $component->getId(), 'progress' => $component->getProgress()));
        $return_value = new __AsyncMessage();
        $return_value->addCommand($command);
        return $return_value;
    }    

    private function _getComponentCommands(__IComponent &$component) {
        $return_value = array();
        //update client end-point with current component state:
        $component->updateClient();
        //get all commands from client end-points
        $binding_codes = $component->getBindingCodes();
        foreach($binding_codes as $binding_code) {
            $ui_binding = __UIBindingManager::getInstance()->getUIBinding($binding_code);
            if($ui_binding != null) {
                $command = $ui_binding->getClientEndPoint()->getCommand();
                if($command != null) {
                    $return_value[] = $command;
                }
            }
        }
        return $return_value;
    }
    
    private function _getComponentSetupCommands(__IComponent &$component) {
        $return_value = array();
        //update client end-point with current component state:
        $component->updateClient();
        //get all commands from client end-points
        $binding_codes = $component->getBindingCodes();
        foreach($binding_codes as $binding_code) {
            $ui_binding = __UIBindingManager::getInstance()->getUIBinding($binding_code);
            if($ui_binding != null) {
                $command = $ui_binding->getClientEndPoint()->getSetupCommand();
                if($command != null) {
                    $return_value[] = $command;
                }
            }
        }
        $handled_events = __EventHandlerManager::getInstance()->getEventHandler($component->getViewCode())->getComponentHandledEvents($component->getName());
        foreach($handled_events as $handled_event) {
            if($handled_event != 'submit') {
                $command = new __AsyncMessageCommand();
                $command->setClass('__RegisterEventListenerCommand');
                $command->setData(array('element' => $component->getId(), 'event' => $handled_event));
                $return_value[] = $command;
            }
        }
        return $return_value;
    }

    
    
}