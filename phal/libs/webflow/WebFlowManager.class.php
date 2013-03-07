<?php

class __WebFlowManager {

    static public $_instance = null;
    
    protected $_flow_definitions = array();
    
    private function __construct() {
        $session = __ApplicationContext::getInstance()->getSession();
        $session_flows = '__WebFlowManager::' . __CurrentContext::getInstance()->getContextId() . '::_flows';
        if($session->hasData($session_flows)) {
            $this->_flow_definitions = $session->getData($session_flows);
        }
        else {
            $this->_flow_definitions = __CurrentContext::getInstance()->getConfiguration()->getSection('configuration')->getSection('webflow');
            $session->setData($session_flows, $this->_flow_definitions);
        }
    }
    
    /**
     * 
     * @return __WebFlowManager
     */
    static public function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __WebFlowManager(); 
        }
        return self::$_instance;
    }
    
    public function hasFlowDefinition($flow_definition_id) {
        $return_value = false;
        if(key_exists($flow_definition_id, $this->_flow_definitions)) {
            $return_value = true;
        }
        return $return_value;
    }
    
    public function getFlowDefinition($flow_definition_id) {
        $return_value = null;
        if(key_exists($flow_definition_id, $this->_flow_definitions)) {
            $return_value = $this->_flow_definitions[$flow_definition_id];
        }
        return $return_value;
    }
    
    
    
}
