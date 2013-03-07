<?php

class __XmlHttpResponse extends __HttpResponse {

    protected $_headers = array();
    protected $_boundary = null;
    protected $_use_x_mixed_replace_content = false;
    protected $_initialized = false;
    
    public function __construct() {
    }

    protected function _initializeXmlHttpResponse() {
        $this->_headers = array('Content-type: application/json');
        $this->_initialized = true;
    }
    
    public function flush() {
        if($this->_initialized == false) {
            $this->_initializeXmlHttpResponse();
        }
        parent::flush();
    }   
    
}