<?php

class __StorageInfo {
    
    private $_file_id = null;
    private $_file_format = null;
    private $_storage_media = STREAM_STORAGE_FILE_SYSTEM;
    private $_storage_media_parameters = array();
    
    public function __construct($file_id) {
        $this->_file_id = $file_id;
    }
    
    public function getFileId() {
        return $this->_file_id;
    }
    
    public function setStorageMedia($storage_media) {
        $this->_storage_media = $storage_media;
    }
    
    public function getStorageMedia() {
        return $this->_storage_media;
    }
     
    public function setFormat($file_format) {
        $this->_file_format = $file_format;
    }
    
    public function getFormat() {
        return $this->_file_format;
    }
    
    public function setStorageParameters(array $storage_parameters) {
        $this->_storage_media_parameters = $storage_parameters;
    }
    
    public function addStorageParameter($parameter_name, $parameter_value) {
        $this->_storage_media_parameters[$parameter_name] = $parameter_value;
    }
    
    public function getStorageParameter($parameter_name) {
        $return_value = null;
        if(key_exists($parameter_name, $this->_storage_media_parameters)) {
            $return_value = $this->_storage_media_parameters[$parameter_name];
        }
        return $return_value;
    }
    
    public function getStorageParameters() {
        return $this->_storage_media_parameters;
    }
    
}
