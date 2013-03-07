<?php

class __AppenderComponent extends __UIComponent {

    protected $_response_writer = null;
    
    public function getResponseWriter() {
        if($this->_response_writer == null) {
            $this->_response_writer = new __ResponseWriter($this->getId());
        }
        return $this->_response_writer;
    }
        
}
