<?php


class __Event {
    
    protected $_raiser_object = null;
    protected $_event_type    = null;
    protected $_parameters    = array();
    
    /**
     * This is the constructor of __Event instance.
     * A __Event instance represent an event (normally it's created when it's needed to be raised).<br>
     * Native PHAL event types are:<br>
     *  - EVENT_ON_SESSION_START: It's raised when a session start
     *  - EVENT_ON_SESSION_FINISH: It's raised when a session finish
     *  - EVENT_ON_REQUEST_START: It's raised for each new request
     *  - EVENT_ON_REQUEST_FINISH: It's raised when a request execution has been finished
     * 
     * NOTE: An application can create and also handle his owns event types (listed events are handled by PHAL).<br>
     * 
     * @param mixed &$raiser_object The object that raiser the event
     * @param integer $event_type The event type
     * @param array $parameters
     */
    public function __construct(&$raiser_object, $event_type) {
        $this->_raiser_object =& $raiser_object;
        $this->_event_type    = $event_type;
    }
    
    /**
     * This method returns a reference to the object that has raised current event.
     * Normally, this is the object that has created current instance
     *
     * @return mixed The object that has raised current event
     */
    public function &getRaiserObject() {
        return $this->_raiser_object;
    }
    
    /**
     * This method returns a code that identify the event type
     *
     * @return integer A code that identify the event type
     */
    public function getEventType() {
        return $this->_event_type;
    }

    public function setParameters(array $parameters) {
        $this->_parameters = $parameters;
    }
    
    /**
     * This method returns all parameters associated to current event.
     *
     * @return array An array with all associated parameters
     */
    public function getParameters() {
        return $this->_parameters;     
    }
    
}