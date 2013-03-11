<?php

class __FileSystemStreamStorage extends __StreamStorage {
    
    protected $_file_handler = null;
    protected $_file_name    = null;
    protected $_required_storage_parameters = array('FILENAME');
    
    public function open($mode) {
        $filename = $this->getFileName();
        $this->_file_name = $filename;
        $this->_file_handler = fopen($this->_file_name, $mode);
    }
    
    private function getFileName() {
        $return_value = $this->getStorageParameter('FILENAME');
        if($this->hasStorageParameter('BASEDIR')) {
            $return_value = $this->getStorageParameter('BASEDIR') . '/' . $return_value;
        }
        return $return_value;        
    }
    
    public function read($length)
    {
        return fread($this->_file_handler, $length);
    }

    public function write($data, $length = null)
    {
        if($length === null) {
            return fwrite($this->_file_handler, $data);
        }
        else {
            return fwrite($this->_file_handler, $data, $length);
        }
    }
    
    public function close() {
        return fclose($this->_file_handler);
    }    

    public function tell()
    {
        return ftell($this->_file_handler);
    }

    public function flush() {
        return fflush($this->_file_handler);
    }
    
    public function eof()
    {
        return feof($this->_file_handler);
    }

    public function lock($operation) {
        return flock($this->_file_handler, $operation);
    }

    public function seek($offset, $whence = null)
    {
        return fseek($this->_file_handler, $offset, $whence);
    }
    
    public function stat() {
        return fstat($this->_file_handler);
    }
    
    public function url_stat() {
        $return_value = array();
        $filename = $this->getFileName();
        if(file_exists($filename)) {
            $return_value = stat($filename);
        }
        return $return_value;
    }
    
}