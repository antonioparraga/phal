<?php


/**
 * This is the __MessageResource class, the most common resource used in phal. A __MessageResource basically contain a message to be rendered.
 * 
 */
class __MessageResource extends __ResourceBase {
    
    protected $_message = null;
    
    public function getMessage() {
        $return_value = $this->_message;
        return $return_value;
    }
    
    public function display() {
        echo $this->_message;
    }
    
}