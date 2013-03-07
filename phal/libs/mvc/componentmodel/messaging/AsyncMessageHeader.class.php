<?php

/**
 * Represents the header of an asynchronous message 
 *
 * @see __AsyncMessage, __AsyncMessageCommand
 */
class __AsyncMessageHeader {
    
    /**
     * The request has succeeded. 
     *
     */
    const ASYNC_MESSAGE_STATUS_OK       = 1;

    /**
     * The server encountered an unexpected condition which prevented it from fulfilling the request. 
     *
     */
    const ASYNC_MESSAGE_STATUS_ERROR    = -1;
    
    /**
     * The client should redirect to another URL
     *
     */
    const ASYNC_MESSAGE_STATUS_REDIRECT = 302;
    
    private $_id       = null;
    private $_status   = null;
    private $_location = null;
    private $_message  = null;

    
    public function __construct() {
        $this->_id = uniqid('m');
        $this->_status  = self::ASYNC_MESSAGE_STATUS_OK;
    }
    
    public function setStatus($status) {
        $this->_status = (int) $status;
    }
    
    public function getStatus() {
        return $this->_status;
    }

    /**
     * Set the URL to redirect to.
     * Setting the URL forces the status to ASYNC_MESSAGE_STATUS_REDIRECT
     *
     * @param string $location
     */
    public function setLocation($location) {
        $this->_location = $location;
        if(!empty($this->_location)) {
            $this->_status = self::ASYNC_MESSAGE_STATUS_REDIRECT;
        }
    }
    
    public function getLocation() {
        return $this->_location;
    }
    
    public function setMessage($message) {
        $this->_message = $message;
    }
    
    public function getMessage() {
        return $this->_message;
    }
    
    public function getId() {
        return $this->_id;
    }
    
    public function toArray() {
        $return_value = array();
        $return_value['id']       = $this->_id;
        $return_value['status']   = $this->_status;
        $return_value['location'] = $this->_location; 
        $return_value['message']  = $this->_message; 
        return $return_value;
    }
    
    
}