<?php

class __ContextEvent extends __Event {
    
    protected $_context_id = null;
    
    public function __construct(&$raiser_object, $event_type, $context_id = null) {
        parent::__construct($raiser_object, $event_type);
        if($context_id == null) {
            $context_id = __CurrentContext::getInstance()->getContextId();
        }
        $this->_context_id = $context_id;
    }    
    
    public function getContextId() {
        return $this->_context_id;
    }
    
}