<?php

class __ContextEventListener extends __EventListener {
    
    protected $_context_id = null;
    
    public function __construct($event_type, __Callback &$callback, $context_id = null) {
        parent::__construct($event_type, $callback);
        if($context_id == null) {
            $context_id = __CurrentContext::getInstance()->getContextId();
        }        
        $this->_context_id = $context_id;
    }  

    public function getContextId() {
        return $this->_context_id;
    }    
    
    /**
     * This method is called by the {@link __EventDispatcher} when a new event is raised and the current {@link __EventListener} is suscribed to it.
     *
     * @param __Event $event The raised event reference
     */
    public function receiveEvent(__Event &$event) {
        if( $event instanceof __ContextEvent && $event->getContextId() == $this->getContextId() ) {
            if($this->_send_event_to_callback == true) {
                $parameters = array();
                $parameters[] =& $event;
                $this->_callback->execute($parameters);
            }
            else {
                $this->_callback->execute();
            }
        }
    }    
    
}