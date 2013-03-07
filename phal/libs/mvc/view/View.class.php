<?php

/**
 * Abstract out of the box class implementing the __IView interface.
 * All out of the box __IView implementation are classes subclassing this one.
 *
 */
abstract class __View implements __IView {
                                          
    /**
     * This variable stores the code for current view<br>
     * This code idenfity the view from the rest of views, and normally it's the View derived class name.
     *
     * @var string
     */
    protected $_code = null;   
    
    protected $_event_handler_class = null;
        
    /**
     * This method sets a code for current view
     *
     * @param string A code for current view
     */
    public function setCode($code) {
        if(is_string($code) && !empty($code)) {
            $this->_code = $code;
        }
    }    
    
    /**
     * This method gets the code associated to the current view. If it's not setted, it will taked from the name of the class instead of
     *
     * @return string The code associated to the current view
     */
    public function getCode() {
        $return_value = $this->_code;
        return $return_value;
    }    

    final public function setEventHandlerClass($event_handler_class) {
        $this->_event_handler_class = $event_handler_class;
    }
    
    final public function getEventHandlerClass() {
        if($this->_event_handler_class != null && class_exists($this->_event_handler_class)) {
            $return_value = $this->_event_handler_class;
        }
        else if($this->_code != null && class_exists($this->_code . 'EventHandler')) {
            $return_value = $this->_code . 'EventHandler';
        }
        else {
            $return_value = __CurrentContext::getInstance()->getPropertyContent('DEFAULT_EVENT_HANDLER_CLASS');
        }
        return $return_value;
    }
    
    protected function _startupRendererEngine() {}
    
    public function setCacheDir($cache_dir) {}
    
    public function setCaching($caching) {}
        
}
