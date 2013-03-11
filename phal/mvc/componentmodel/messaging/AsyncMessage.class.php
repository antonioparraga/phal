<?php

class __AsyncMessage {
    
    protected $_header = array();
    protected $_commands = array();
    
    public function __construct() {
        $this->_header = new __AsyncMessageHeader();
    }
    
    public function setHeader(__MessageHeader $message_header) {
        $this->_header = $message_header;
    }
    
    /**
     * Enter description here...
     *
     * @return __AsyncMessageHeader
     */
    public function &getHeader() {
        return $this->_header;
    }
    
    public function setCommands(array $commands) {
        $this->_commands = $commands;
    }
    
    public function addCommand(__AsyncMessageCommand &$command) {
        if($command != null) {
            $this->_commands[] =& $command;
        }
    }
    
    public function hasPayload() {
        return count($this->_commands) > 0;
    }
    
    public function &getCommands() {
        return $this->_commands;
    }
    
    public function toArray() {
        $return_value = array();
        //set the header:
        $return_value['header'] = $this->_header->toArray();
        $return_value['commands'] = array();
        foreach($this->_commands as $command) {
            $return_value['commands'][] = $command->toArray();
        }
        return $return_value;        
    }
    
    public function toJson() {
        $async_message_array = $this->toArray();        
        $return_value = json_encode($this->_convertArrayToUtf8($async_message_array));
        return $return_value;
    }
    
    protected function _convertArrayToUtf8(array $array) { 
        $convertedArray = array(); 
        foreach($array as $key => $value) { 
            if(!mb_check_encoding($key, 'UTF-8')) { 
                $key = utf8_encode($key); 
            }
           
            if(is_array($value)) {
                $value = $this->_convertArrayToUtf8($value); 
            } 
            else if(!mb_check_encoding($value, 'UTF-8')) { 
                $value = utf8_encode($value); 
            } 
          
            $convertedArray[$key] = $value; 
        } 
        return $convertedArray; 
    } 
    
    
    public function __toString() {
        return $this->toJson();
    }    
    
}