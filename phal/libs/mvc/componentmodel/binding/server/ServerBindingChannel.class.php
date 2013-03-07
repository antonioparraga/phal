<?php

class __ServerBindingChannel {
    
    private $_binding_codes = array();
    static private $_instance = null;
    
    private $_ui_bindings = array();
    
    private function __construct() {
        $session = __CurrentContext::getInstance()->getSession();
        if($session->hasData('__ServerBindingChannel::_binding_codes')) {
            $this->_binding_codes =& $session->getData('__ServerBindingChannel::_binding_codes');
        }
        else {
            $session->setData('__ServerBindingChannel::_binding_codes', $this->_binding_codes);
        }
    }
    
    /**
     * Gets a {@link __ServerBindingChannel} singleton instance
     *
     * @return __ServerBindingChannel
     */
    static public function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __ServerBindingChannel();
        }
        return self::$_instance;
    }
    
    public function addServerEndPoint(__IServerEndPoint $server_end_point) {
        $component_id = $server_end_point->getComponent()->getId();
        if(key_exists($component_id, $this->_binding_codes)) {
            $this->_binding_codes[$component_id] = array();
        }
        $server_end_point->getUIBinding;
    }
    
    
    
    
    
}