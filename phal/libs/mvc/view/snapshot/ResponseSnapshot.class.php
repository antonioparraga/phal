<?php

class __ResponseSnapshot {

    protected $_response = null;
    protected $_view_codes = array();
    protected $_ui_bindings = null;
    
    public function __construct(__IResponse &$response) {
        $this->setResponse($response);
    }
    
    public function setResponse(__IResponse &$response) {
        $response->prepareToSleep();
        $this->_response =& $response;
        $this->_view_codes = array();
        $view_codes = $response->getViewCodes();
        foreach($view_codes as $view_code) {
            $this->_view_codes[$view_code] = true;
        }
    }
    
    public function &getResponse() {
        return $this->_response;
    }
    
    public function areViewsRestorable() {
         $return_value = true; //by default we're going to read the view from the cache:
         if(!__AuthenticationManager::getInstance()->isAnonymous()) {
             $return_value = false;
         }
         else {
             $component_handler_manager = __ComponentHandlerManager::getInstance();
             foreach($this->_view_codes as $view_code => $dummy) {
                 if($component_handler_manager->hasComponentHandler($view_code) && 
                    $component_handler_manager->getComponentHandler($view_code)->isDirty()) {
                    //do not read from the cache if the component handler is dirty
                     $return_value = false;
                 }
             }
         }
         return $return_value;
    }
    
}
