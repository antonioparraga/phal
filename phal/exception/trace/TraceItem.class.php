<?php

class __TraceItem {

    protected $_file = null;
    protected $_line = null;
    protected $_function = null;
    protected $_arguments = array();
    protected $_file_source = null;
    protected $_class = null;
    protected $_type = null;
    
    public function setFile($file) {
        $this->_file = $file;
    }
    
    public function getFile() {
        return $this->_file;
    }
    
    public function setLine($line) {
        $this->_line = $line;
    }
    
    public function getLine() {
        return $this->_line;
    }

    public function setFunction($function) {
        $this->_function = $function;
    }
    
    public function getFileSource() {
        if($this->_file_source === null && is_file($this->_file) && is_readable($this->_file)) {
            $this->_file_source = file($this->_file);
        }
        return $this->_file_source;
    }
    
    public function getFunction() {
        return $this->_function;
    }
    
    public function setArguments($arguments) {
        $this->_arguments = $arguments;
    }
    
    public function getArguments() {
        return $this->_arguments;
    }
    
    public function getCodeLine() {
        return $this->_code_line;
    }
    
    public function setClass($class) {
        $this->_class = $class;
    }
    
    public function getClass() {
        return $this->_class;
    }
    
    public function setType($type) {
        $this->_type = $type;
    }
    
    public function getType() {
        return $this->_type;
    }
    
    public function getCall() {
        $return_value = '';
        if($this->_class !== null) {
            $return_value = $this->_class;
        }
        if($this->_type !== null) {
            $return_value .= $this->_type;
        }
        if($this->_function !== null) {
            $return_value .= $this->_function;
        }
        return $return_value;
    }
    
    public function getCodeAroundAsHtml() {
        $return_value = null;
        if(file_exists($this->_file) && is_readable($this->_file)) {
            $file_source = preg_split('/\<br \/\>/', highlight_file($this->_file, true));
            if($file_source !== null && $this->_line !== null) {
                $first_line = max(0, $this->_line - 4);
                $lines_offset = min(count($file_source), 7);
                $source_code = '';
                for($i = 0; $i < $lines_offset; $i++) {
                    if($i + $first_line + 1 == $this->_line) {
                        $source_code .= '<li style="list-style-position: inside; padding: 0px; margin: 0px; font-family: monospace; background-color: #ffcccc; font-weight: bold;">';
                    }
                    else {
                        $source_code .= '<li style="list-style-position: inside; padding: 0px; margin: 0px; font-family: monospace;">';
                    }
                    if(key_exists($i + $first_line, $file_source)) {
                        $source_code .= $file_source[$i + $first_line];
                    }
                    $source_code .= '</li>';
                }
                
                $return_value = '<ol style="border:1px dashed #A0AAA0; font-family:monospace;padding: 5px; margin: 0px;" start="' . ($first_line + 1) . '">' . $source_code . '</ol>';
                
            }
        }
        return $return_value;
    }
    
}
