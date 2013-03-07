<?php


class __ActionControllerResolver {

    private static $_instance = null;

    private $_controller_definitions = array();

    private function __construct() {
        $controller_definitions = __ContextManager::getInstance()->getCurrentContext()->getConfiguration()->getSection('configuration')->getSection('controller-definitions');
        if(is_array($controller_definitions)) {
            $this->_controller_definitions = $controller_definitions;
        }
    }

    /**
     * This method return a singleton instance of __ActionFactory instance
     *
     * @return __ActionFactory a reference to the current __ActionFactory instance
     */
    public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __ActionControllerResolver();
        }
        return self::$_instance;
    }
    
    public function getActionController($action_controller_code) {
        $controller_definition = $this->getActionControllerDefinition($action_controller_code);
        if($controller_definition instanceof __ActionControllerDefinition) {
            $action_controller = __ActionControllerFactory::createActionController($controller_definition, $action_controller_code);
        }
        if($action_controller == null) {
            throw __ExceptionFactory::getInstance()->createException('ERR_CAN_NOT_RESOLVE_CONTROLLER', array($action_controller_code));
        }
        return $action_controller;
    }
    
    public function getActionControllerDefinition($action_controller_code) {
        $return_value = null;
        if(!empty($action_controller_code)) {
            $cache = __ApplicationContext::getInstance()->getCache();
            $controller_definition_cache_key = '__ActionControllerDefinitions__' . $action_controller_code;
            $return_value = $cache->getData($controller_definition_cache_key);
            if($return_value == null) {
                $action_controller_code = strtoupper(trim($action_controller_code));
                if(key_exists($action_controller_code, $this->_controller_definitions['static_rules'])) {
                    $return_value = $this->_controller_definitions['static_rules'][$action_controller_code];
                }
                //check dynamic rules:
                else {
                    foreach($this->_controller_definitions['dynamic_rules'] as &$controller_definition) {
                        if( $controller_definition->isValidForControllerCode($action_controller_code)) {
                            $return_value = $controller_definition;
                        }
                    }
                }
                $cache->setData($controller_definition_cache_key, $return_value);
            }
        }
        return $return_value;
    }

}