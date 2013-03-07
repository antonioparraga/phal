<?php

abstract class __StreamStorage {
    
    protected $_required_storage_parameters = array();
    protected $_storage_parameters = null;
    
    final public function __construct(array $storage_parameters = array()) {
        $storage_parameters = array_change_key_case($storage_parameters, CASE_UPPER);
        foreach ($this->_required_storage_parameters as $required_storage_parameter) {
            if(!key_exists($required_storage_parameter, $storage_parameters)) {
                throw new __StreamException("Missing required parameter to build an instance of '" . get_class($this) . "': '" . $required_storage_parameter . "'");
            }
        }
        $this->_storage_parameters = $storage_parameters;
    }
        
    final public function getStorageParameter($parameter_name) {
        $return_value = null;
        $parameter_name = strtoupper($parameter_name);
        if(key_exists($parameter_name, $this->_storage_parameters)) {
            $return_value = $this->_storage_parameters[$parameter_name];
        }
        return $return_value;
    }
    
    final public function hasStorageParameter($parameter_name) {
        return key_exists($parameter_name, $this->_storage_parameters);
    }
    
    abstract public function open($mode);
    
    abstract public function read($length);

    abstract public function write($data, $length = null);
    
    abstract public function close();

    abstract public function tell();
    
    abstract public function flush();

    abstract public function eof();
    
    abstract public function lock($operation);
    
    abstract public function seek($offset, $whence = null);
    
    abstract public function stat();

    abstract public function url_stat();
    
}