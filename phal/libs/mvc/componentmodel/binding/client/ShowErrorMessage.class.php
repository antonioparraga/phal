<?php

/**
 * This end-point class is used to show validation messages to client
 *
 */
class __ShowErrorMessage extends __ClientEndPoint {
    
    protected $_instance = null;
    protected $_message = null;
    protected $_bound_direction = __IEndPoint::BIND_DIRECTION_S2C;    
    
    public function __construct($instance) {
        $this->setInstance($instance);
    }
    
    /**
     * Enter description here...
     *
     * @param unknown_type $instance
     */
    public function setInstance($instance) {
        $this->_instance = $instance;
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getInstance() {
        return $this->_instance;
    }
    
    public function setErrorMessage($message) {
        $this->_message = $message;
    }
    
    public function getErrorMessage() {
        return $this->_message;
    }

    public function synchronize(__IServerEndPoint &$server_end_point) {
        if( $server_end_point instanceof __IValueHolder ) {
            $this->setErrorMessage( $server_end_point->getValue() );
        }
    }
    
    /**
     * Gets the startup command representing the current end-point
     *
     * @return __AsyncMessageCommand
     */
    public function getSetupCommand() {
        return null;
    }

    /**
     * Get a command representing the current end-point
     *
     * @return __AsyncMessageCommand
     */
    public function getCommand() {
        $return_value = null;
        $data = array();
        $data['message']  = $this->_message;
        $data['receiver'] = $this->_instance;
        $return_value = new __AsyncMessageCommand();
        $return_value->setClass('__ShowValidationErrorCommand');
        $return_value->setData($data);
        return $return_value;             
    }    
    
    
}

