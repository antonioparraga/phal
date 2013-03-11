<?php

class __IniFileResourceProvider extends __ResourceProvider {
        
    protected $_language_dir = null;
    protected $_filename = null;
    protected $_encoding = null;
    
    public function getLanguageFile($language_iso_code, __ActionIdentity $action_identity = null) {
        if($action_identity != null) {
            $filename = $action_identity->getControllerCode() . '.ini';
        }
        else {
            $filename = $this->_filename;
        }
        $return_value = $this->_language_dir . DIRECTORY_SEPARATOR . $language_iso_code . DIRECTORY_SEPARATOR . $filename;
        return $return_value;
    }
    
    public function setFileEncoding($encoding) {
        $this->setEncoding($encoding);
    }
    
    public function getFileEncoding() {
        return $this->getEncoding();
    }
    
    public function setEncoding($encoding) {
        $this->_encoding = $encoding;
    }
    
    public function getEncoding() {
        return $this->_encoding;
    }

    public function setLanguageDir($language_dir) {
        $this->_language_dir = __PathResolver::resolvePath($language_dir);
    }
    
    public function getLanguageDir() {
        return $this->_language_dir;
    }
    
    public function setFilename($filename) {
        $this->_filename = $filename;
    }
    
    public function getFilename() {
        return $this->_filename;
    }

    public function loadResources($language_iso_code, __ActionIdentity $action_identity = null) {
        $language_file = $this->getLanguageFile($language_iso_code, $action_identity);
        $return_value = array();
        if(is_file($language_file) && is_readable($language_file)) {
            $resources = parse_ini_file($language_file, false);
            foreach($resources as $key => $value) {
                if($this->_encoding != null) {
                    $value = iconv($this->_encoding, iconv_get_encoding("internal_encoding"), $value);
                }
                $resource = $this->_createResource($key, $value);
                $return_value[$key] = $resource;
                unset($resource);
            }
        }
        return $return_value;
    }
    
}