<?php

class __StreamStorageFactory {
        
    private static $_instance = null;
    private $_storage_classes = array();
    
    private function __construct() {
        //startup storage classes:
        $this->addStorageClass(STREAM_STORAGE_FILE_SYSTEM, '__FileSystemStreamStorage');
    }
    
    public function addStorageClass($storage_media, $storage_class) {
        if(class_exists($storage_class)) {
            $this->_storage_classes[$storage_media] = $storage_class;
        }
        else {
            throw new __StreamException("Unknow stream storage class: '" . $storage_class . "'");
        }
    }
    
    public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __StreamStorageFactory();
        }
        return self::$_instance;
    }    
    
    public function createStreamStorage(__StorageInfo &$storage_info) {
        $storage_media      = $storage_info->getStorageMedia();
        $storage_parameters = $storage_info->getStorageParameters();
        return $this->_createStreamStorageOfMedia($storage_media, $storage_parameters);
    }
    

    private function _createStreamStorageOfMedia($storage_media, array $storage_parameters = array()) {
        $return_value = null;
        if(key_exists($storage_media, $this->_storage_classes)) {
            $storage_class = $this->_storage_classes[$storage_media];
            $return_value = new $storage_class($storage_parameters);
        }
        return $return_value;
    }
    
    
}