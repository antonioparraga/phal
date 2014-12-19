<?php

/**
 * This class is an implementation of the {@link __IEventHandler}, 
 * a class in charge of handling UI component events in a view.
 * 
 */
class __EventHandler implements __IEventHandler {

    protected $_view_code = null;
    protected $_parent_view_code = null;
    protected $_actionbox_id = null;
    
    public function setViewCode($view_code) {
        $this->_view_code = $view_code;
    }
    
    public function getViewCode() {
        return $this->_view_code;
    }
    
    /**
     * Sets the parent view code, corresponding to a parent viewport (if applicable)
     * i.e., if current view is contained in an actionbox, the parent viewcode is the view containing the actionbox
     *
     * @param string $parent_view_code
     */
    public function setParentViewCode($parent_view_code) {
        $this->_parent_view_code = $parent_view_code;
    }
    
    /**
     * Gets the parent view code associated to current event handler (if applicable)
     * 
     * @return string
     *
     */
    public function getParentViewCode() {
        return $this->_parent_view_code;
    }
    
    /**
     * Set the identifier of the {@link __ActionBoxComponent} where current view has been rendered in (if applicable)
     *
     * @param string $actionbox_id
     */
    public function setContainerActionBoxId($actionbox_id) {
        $this->_actionbox_id = $actionbox_id;
    }
    
    /**
     * Get the identifier of the {@link __ActionBoxComponent} where current view has been rendered in (if applicable)
     *
     * @return string
     */
    public function getContainerActionBoxId() {
        return $this->_actionbox_id;
    }
    
    /**
     * Get the event handler correponding to the parent view (if applicable)
     * 
     * @return __IEventHandler
     *
     */
    public function getParentEventHandler() {
        $return_value = null;
        if($this->_parent_view_code != null && __EventHandlerManager::getInstance()->hasEventHandler($this->_parent_view_code)) {
            $return_value = __EventHandlerManager::getInstance()->getEventHandler($this->_parent_view_code);
        }
    }
    
    /**
     * Handles an UI event by executing a method associated to the given event
     *
     * @param __UIEvent $event The event to handle
     * @return mixed
     */
    public function handleEvent(__UIEvent &$event) {
        $return_value    = null;
        //get event info:
        $component       = $event->getComponent();
        $event_name      = $event->getEventName();
        $extra_info      = $event->getExtraInfo();

        //todo...
        
        
        
        return $return_value;
        
    }
    
    protected function _getEventHandlerMethod($event_name, $component_name) {
        $return_value = $component_name . '_' . $event_name;
        return $return_value;
    }
    
    public function create() {
        //nothing to do
    }
    
    public function beforeRender() {
        //nothing to do
    }

    public function afterRender() {
        //nothing to do
    }
    
    
}