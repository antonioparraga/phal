<?php

class __CompositeWriter extends __ComponentWriter implements __ICompositeWriter {
    
    protected $_view_definition = null;
    
    public function bindComponentToClient(__IComponent &$component) {
        $component->setCompositeWriter($this);
        $sep = new __ComponentProperty($component, 'content');
        $cep = new __HtmlElementCallback($component->getId(), 'update');
        $cep->setSynchronizationPrefilterCallback(new __Callback($component, 'isUnsynchronized'));
        __UIBindingManager::getInstance()->bindFromServerToClient($sep, $cep);
        parent::bindComponentToClient($component);
    }
    
    public function setViewDefinition(__ViewDefinition &$view_definition) {
        $this->_view_definition = $view_definition;
    }
    
    public function createView() {
        $return_value = null;
        if($this->_view_definition != null) {
            $return_value = $this->_view_definition->getView();
        }
        return $return_value;
    }
    
    public function startRender(__IComponent &$component) {
        if($component instanceof __ICompositeComponent) {
            $return_value = null;
            $component_id = $component->getId();
            $view = $this->createView();
            if($view instanceof __IView) {
                //set the view code as the component identifier:
                $view->setCode($component_id);
                $event_handler = null;
                if(!__EventHandlerManager::getInstance()->hasEventHandler($component_id)) {
                    $event_handler_class = $view->getEventHandlerClass();
                    if($event_handler_class != null) {
                        $event_handler = new $event_handler_class();
                        if($event_handler instanceof __ICompositeComponentEventHandler) {
                            //set the view code:
                            $event_handler->setViewCode($component_id);
                            //relate the component with the event handler:
                            $event_handler->setCompositeComponent($component);
                            $component->setEventHandler($event_handler);
                            __EventHandlerManager::getInstance()->addEventHandler($event_handler);
                        }
                        else {
                            throw __ExceptionFactory::getInstance()->createException('Event handler associated to a composite components must implement __ICompositeComponentEventHandler. ' . get_class($event_handler) . ' class does not implement this interface');
                        }
                    }
                }
                //assign the component to the view:
                $this->_assignComponentToView($view, $component);
                //execute the view to render the component:
                $return_value = $view->execute();
            }
            $component->setContent($return_value);
            return '<span id="' . $component_id . '">' . $return_value . '</span>';
        }
        else {
            throw __ExceptionFactory::getInstance()->createException(get_class($component) . ' class not expected, but an instance of a class implementing __ICompositeComponent.');
        }
    }
    
    protected function _assignComponentToView(__IView &$view, __IComponent &$component) {
        $view->assign('component', $component);
    }
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        return null;
    }
    
}
    