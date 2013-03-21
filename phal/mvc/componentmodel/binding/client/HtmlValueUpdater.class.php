<?php

class __HtmlValueUpdater extends __ClientEndPoint {
    
    protected $_instance  = null;
    protected $_bound_direction = __IEndPoint::BIND_DIRECTION_S2C;   
    protected $_execute_always = false;     
    
    public function __construct($instance) {
        $this->setInstance($instance);
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
            $data['html'] = $this->_value;
            $data['receiver']  = $this->_instance;
            $return_value = new __AsyncMessageCommand();
            $return_value->setClass($this->getClientCommandClass());
            $return_value->setData($data);
            $this->setAsSynchronized();
        }
        return $return_value;                              
    }
            
    public function getClientCommandClass() {
        return '__UpdateHtmlContentCommand';
    }
    
}