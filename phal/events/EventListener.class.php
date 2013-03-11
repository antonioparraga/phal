<?php


/**
 * This is the class in charge of listen for any event raise.
 * This class suscribe itself to certain set of events, and for each one stores a callback. If any event is raised,
 * then it will call to the callback method by passing a reference to the {@link __Event}.
 *
 */
class __EventListener {

    /**
     * This variable stores all events that current EventListener is suscribed to.
     *
     * @var array
     */
    protected $_event_to_listen = null;
    
    /**
     * This variable stores all callback associated to each suscribed event.
     *
     * @var array
     */
    protected $_callback = null;
    
    /**
     * If a reference to the {@link __Event} instance should be send to the callback
     *
     * @var bool
     */
    protected $_send_event_to_callback = true;

    public function __construct($event_type, __Callback &$callback) {
        $this->_event_to_listen = $event_type;
        $this->_callback        =& $callback;
    }
    
    /**
     * This is the destructor method. It calls to the {@link stopToListen} method
     *
     */
    final public function __destruct() {
    }

    /**
     * This method starts to listen for any event raise, by calling to any callback associated to each event.
     *
     */
    final public function startToListen() {
        __EventDispatcher::getInstance()->registerEventListener($this);
    }

    /**
     * This method stop to listen for any event raise. No actions will be executed for any event.
     *
     */
    final public function stopToListen() {
        __EventDispatcher::getInstance()->unregisterEventListener($this);
    }

    /**
     * This method returns the list of events that current __EventListener is suscribed for.
     *
     * @return array
     */
    final public function getEventToListen() {
        return $this->_event_to_listen;
    }
    
    /**
     * This method is called by the {@link __EventDispatcher} when a new event is raised and the current {@link __EventListener} is suscribed to it.
     *
     * @param __Event $event The raised event reference
     */
    public function receiveEvent(__Event &$event) {
        $parameters = array();
        $parameters[] =& $event;
        $this->_callback->execute($parameters);
    }

}
