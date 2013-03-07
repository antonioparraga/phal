<?php

class __ResponseWriterManager {
    
    static private $_instance = null;
    
    private $_response_writers = array();
    
    private function __construct() {
    }
    
    /**
     * Gets a reference to the singleton {@link __ResponseWriterManager} instance
     *
     * @return __ResponseWriterManager
     */
    static public function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __ResponseWriterManager();
        }
        return self::$_instance;
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
    
    public function write(__IResponse &$response) {
        foreach($this->_response_writers as $response_writer) {
            $response_writer->write($response);
        }
    }
    
    public function clear() {
        $this->_response_writers = array();
    }
    
}