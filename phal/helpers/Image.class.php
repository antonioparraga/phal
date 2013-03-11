<?php

class __Image {
    
    private $_name = null;
    private $_mime_type   = 'image/gif';
    private $_base64_data = null;
    
    public function __construct($name, $filename = null) {
        $this->_name = $name;
        if($filename != null) {
            //todo: populate from file            
        }
    }
    
    public function getName() {
        return $this->_name;
    }
    
    public function setBase64Data($base64_data) {
        $this->_base64_data = trim($base64_data);
    }
    
    public function getBase64Data() {
        return $this->_base64_data;
    }
    
    public function setMimeType($mime_type) {
        $this->_mime_type = $mime_type;
    }
    
    public function getMimeType() {
        return $this->_mime_type;
    }
    
    public function getDataUri() {
        $return_value = "data:" . $this->_mime_type . ";base64," . $this->_base64_data;
        return $return_value;
    }

    public function getMhtmlUri() {
        $return_value = "local:/$this->_name";
        return $return_value;
    }
    
    public function getMhtmlNextPart() {
        $return_value = "        
------=_NextPart
Content-Location: local:/$this->_name
Content-Transfer-Encoding: base64
Content-Type: $this->_mime_type

$this->_base64_data

";
        return $return_value;
    }

}
