<?php

class __CssResponseWriter implements __IResponseWriter {

    protected $_id = null;
    protected $_css_rules = array();
    protected $_response_writers = array();
    
    public function __construct($id) {
        if(empty($id)) {
            throw __ExceptionFactory::getInstance()->createException('A valid id is required to instantiate a __ResponseWriter object');
        }
        $this->_id = $id;
    }
    
    public function getId() {
        return $this->_id;
    }
    
    public function addCssRules($rules) {
        $this->_css_rules[] = $rules;
    }
    
    public function addRules($rules) {
        return $this->addCssRules($rules);
    }
    
    public function getContent() {
        $return_value = "\n<style>\n" . 
                        implode("\n", $this->_css_rules) .
                        "\n</style>\n";
        $return_value .= $this->_getChildrensContent();
        return $return_value;
    }
    
    protected function _getChildrensContent() {
        $return_value = '';
        foreach($this->_response_writers as $response_writer) {
            $return_value .= $response_writer->getContent();
        }
        return $return_value;
    }    
        
    public function __toString() {
        return $this->getContent();
    }
    
    public function write(__IResponse &$response) {
        $response->dockContentOnTop($this->getContent(), $this->getId());
    }
    
    public function hasResponseWriter($id) {
        $return_value = false;
        if(key_exists($id, $this->_response_writers)) {
            $return_value = true;
        }
        else {
            foreach($this->_response_writers as &$response_writer) {
                if($response_writer->hasResponseWriter($id)) {
                    return true;
                }
            }
        }
        return $return_value;
    }
    
    public function &getResponseWriter($id) {
        $return_value = null;
        if(key_exists($id, $this->_response_writers)) {
            $return_value =& $this->_response_writers[$id];
        }
        else {
            foreach($this->_response_writers as &$response_writer) {
                $return_value = $response_writer->getResponseWriter($id);
                if($return_value != null) {
                    return $return_value;
                }
            }
        }
        return $return_value;
    }
    
    public function addResponseWriter(__IResponseWriter $response_writer) {
        $this->_response_writers[$response_writer->getId()] = $response_writer;
    }
    
    public function clear() {
        $this->_response_writers = array();
        $this->_css_rules = array();
    }    

   
}