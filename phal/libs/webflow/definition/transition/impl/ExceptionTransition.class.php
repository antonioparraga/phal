<?php

class __ExceptionTransition extends __FlowTransition {

    protected $_exception = null;
    
    public function onException($exception) {
        $this->setException($exception);
    }
    
    public function setException($exception) {
        $this->_exception = $exception;
    }
    
    public function getException() {
        return $this->_exception;
    }        
    
}
