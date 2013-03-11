<?php

/**
 * This class represents an end-point to a client method, which will be executed in case the server end-point need to be synchronized to client side.
 *
 */
abstract class __ClientCallback extends __ClientEndPoint {
    
    protected $_instance  = null;
    protected $_method    = null;
    protected $_bound_direction = __IEndPoint::BIND_DIRECTION_S2C;   
    protected $_execute_always = false;     
    
    public function __construct($instance, $method) {
        $this->setInstance($instance);
        $this->setMethod($method);
    }
    
    public function setExecuteAlways($execute_always) {
        $this->_execute_always = (bool) $execute_always;
    }
    
    public function getExecuteAlways() {
        return $this->_execute_always;
    }
    
    /**
     * Set the client instance identifier that contains the method to be executed to (i.e. a javascript variable name or an html element id)
     *
     * @param string $instance
     */
    public function setInstance($instance) {
        $this->_instance = $instance;
    }
    
    /**
     * Get the client instance identifier that contains the method to be executed to
     *
     * @return string
     */
    public function getInstance() {
        return $this->_instance;
    }
    
    /**
     * Set the name of the method to be executed to
     *
     * @param string $method
     */
    public function setMethod($method) {
        $this->_method = $method;
    }
    
    /**
     * Get the name of the method to be executed to
     *
     * @return string
     */
    public function getMethod() {
        return $this->_method;
    }
    
    /**
     * Callbacks are not executed on startup, but set as synchronized the current end-point
     *
     * @return null
     */
    public function getSetupCommand() {
        $this->setAsSynchronized();
        return null;
    }

    /**
     * Get the command representing the call to the client-side method and set the current end-point as synchronized
     *
     * @return __AsyncMessageCommand
     */
    public function getCommand() {
        $return_value = null;
        if($this->_execute_always || $this->isUnsynchronized()) {
            $data = array();
            $data['parameter'] = $this->_value;
            $data['receiver']  = $this->_instance;
            $data['method']    = $this->_method;
            $return_value = new __AsyncMessageCommand();
            $return_value->setClass($this->getClientCommandClass());
            $return_value->setData($data);
            $this->setAsSynchronized();
        }
        return $return_value;                              
    }
    
    /**
     * Abstract method that inform about the client command class to be used to execute the callback
     * 
     */
    abstract public function getClientCommandClass();
    
}