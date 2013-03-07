<?php


/**
 * Represents a client request and provides common methods to all request types.
 * 
 * Note that children classes represent concrete request types (requests by command line, by http, ...)
 * 
 * <code>
 * 
 * //Get the request that the front controller is dispatching (usually, the client request):
 * $request = __FrontController::getInstance()->getRequest();
 * 
 * //Get the request that the __ActionDispatcher is ussing to execute the current action (can differ from the client request):
 * $request = __ActionDispatcher::getInstance()->getRequest();
 * 
 * //Check if page_number parameter has been specified:
 * if($request->hasParameter('page_number')) {
 *    //Get the page number:
 *    $page_number = $request->getParameter('page_number');
 * }
 * 
 * </code>
 * 
 */
abstract class __Request implements __IRequest {

    protected $_request_method = REQMETHOD_NONE;
    protected $_requested_parameters = array( REQMETHOD_ALL => array() );
    protected $_attributes = array();
    protected $_request_type = null;
        
    public function setRequestMethod($request_method) {
        $this->setMethod($request_method);
    }
    
    public function setMethod($request_method) {
        $this->_request_method = $request_method;
    }
    
    public function getRequestMethod() {
        return $this->getMethod();
    }
    
    /**
     * Returns the last used requested method. i.e. GET or POST
     *
     * @return integer A numeric value that represent a valid request method
     */
    public function getMethod() {
    	return $this->_request_method;
    }    
    
    /**
     * Alias of addParameter
     *
     * @param string $parameter_name A name that identify the parameter to set to
     * @param string $parameter_value The value of the parameter
     */
    public function setParameter($parameter_name, $parameter_value, $request_method = null) {
        $this->addParameter($parameter_name, $parameter_value, $request_method);
    }
    
    /**
     * Adds a request parameter (as a pair [name, value]) to the current Request instance
     * 
     * @param string $parameter_name A name that identify the parameter to set to
     * @param string $parameter_value The value of the parameter
     */
    public function addParameter($parameter_name, $parameter_value, $request_method = null) {
        $parameter_name = strtolower($parameter_name);
        $this->_requested_parameters[REQMETHOD_ALL][$parameter_name] = $parameter_value;
        if($request_method != null) {
            if(!key_exists($request_method, $this->_requested_parameters)) {
                $this->_requested_parameters[$request_method] = array();
            }
            $this->_requested_parameters[$request_method][$parameter_name] = $parameter_value;
        }
    }
    
    /**
     * Populates request parameters from a given associative array of pairs key, value
     *
     * @param array $parameters
     */
    public function fromArray(array $parameters) {
        $this->_requested_parameters = array();
        $parameters = array_change_key_case($parameters, CASE_LOWER);
        $this->_requested_parameters[REQMETHOD_ALL] = $parameters;
    }
    
    /**
     * Transform the current request parameters into an array
     * 
     * @param $request_method optional, the request method to get the parameters from
     * @return array
     */
    public function toArray($request_method = null) {
        if($request_method != null) {
            if(key_exists($request_method, $this->_requested_parameters)) {
                $return_value = $this->_requested_parameters[$request_method];
            }
            else {
                throw __ExceptionFactory::createException('Unknow request method: ' . $request_method);
            }
        }
        else {
            $return_value = $this->_requested_parameters[REQMETHOD_ALL];
        }
        return $return_value;
    }
    
    /**
     * Returns a specified requested parameter value
     *
     * @param string $parameter_name The parameter name
     * @param integer $request_method The request method to retrieve the parameter from
     * @return string The value of the parameter or null if doesn't exist
     */
    public function getParameter($parameter_name, $request_method = null) {
        $parameter_name = strtolower($parameter_name);
        if($request_method == null) {
            $request_method = REQMETHOD_ALL;
        }
    	$return_value = null;
    	if(key_exists($request_method, $this->_requested_parameters) && key_exists($parameter_name, $this->_requested_parameters[$request_method])) {
        	$return_value = $this->_requested_parameters[$request_method][$parameter_name];
    	}
    	return $return_value;
    }
    
    /**
     * Returns an array with all parameters for current Request instance
     *
     * @return array An array of all parameters
     */
    public function getAllParameters() {
        return $this->_requested_parameters[REQMETHOD_ALL];
    }

    /**
     * This method is an alias of {@link getAllParameters} method
     *
     * @return array An array of all parameters
     */
    public function getParameters($request_method = null) {
        $return_value = null;
        if($request_method == null) {
            $return_value = $this->getAllParameters();
        }
        else {
            if(key_exists($request_method, $this->_requested_parameters)) {
                $return_value = $this->_requested_parameters[$request_method];
            }
        }
        return $return_value;
    }

    /**
     * Unset (remove from current __Request) a specified parameter
     * 
     * @param string $parameter_name The parameter name to unset to
     */
    public function unsetParameter($parameter_name) {
        $parameter_name = strtolower($parameter_name);
        if(key_exists($parameter_name, $this->_requested_parameters[REQMETHOD_ALL])) {
            unset($this->_requested_parameters[REQMETHOD_ALL][$parameter_name]);
        }
        $request_method = $this->getMethod();
        if( key_exists($request_method, $this->_requested_parameters) && key_exists($parameter_name, $this->_requested_parameters[$request_method]) ) {
            unset($this->_requested_parameters[$request_method][$parameter_name]);
        }
    }
    
    /**
     * Checks if exists a parameter associated to the current __Request instance
     *
     * @param string $parameter_name The parameter's name to check to
     * @return bool true if exists, else false
     */
    public function hasParameter($parameter_name) {
        $parameter_name = strtolower($parameter_name);
        return key_exists($parameter_name, $this->_requested_parameters[REQMETHOD_ALL]);
    }

    /**
     * Adds a pair [attribute, value]. Useful to add more values to the request without altering the request parameters
     * 
     * @param $attribute_name
     * @param $attribute_value
     * 
     */
    public function addAttribute($attribute_name, $attribute_value) {
        $this->_attributes[$attribute_name] = $attribute_value;
    }
    
    /**
     * Gets a given attribute from the request
     * 
     * @param $attribute_name
     * @return mixed The value of the given attribute
     */
    public function getAttribute($attribute_name) {
        if(key_exists($attribute_name, $this->_attributes)) {
            return $this->_attributes[$attribute_name];
        }
        else {
            return null;
        }
    }
    
    /**
     * Checks if a given attribute has been previously set to
     * 
     * @param $attribute_name
     * @return boolean
     */
    public function hasAttribute($attribute_name) {
        if(key_exists($attribute_name, $this->_attributes)) {
            return true;
        }
        else {
            return false;
        }
    }
    
    /**
     * Retrieves all the attributes and their values set in the current request
     * 
     * @return array associative array, keys are attribute names.
     */
    public function getAttributes() {
        return $this->_attributes;
    }    
    
    /**
     * Set the parameters for controller + action by specifying an action identity (which contains both values)
     *
     * @param __ActionIdentity $action_identity
     */
    public function setActionIdentity(__ActionIdentity $action_identity) {
        $this->addParameter(__ApplicationContext::getInstance()->getPropertyContent('REQUEST_CONTROLLER_CODE'), $action_identity->getControllerCode());
        $this->addParameter(__ApplicationContext::getInstance()->getPropertyContent('REQUEST_ACTION_CODE'), $action_identity->getActionCode());
    }    
    
    /**
     * Enter description here...
     *
     * @return __ActionIdentity
     */
    public function getActionIdentity() {
        $return_value = new __ActionIdentity();
        if($this->hasParameter(__ApplicationContext::getInstance()->getPropertyContent('REQUEST_CONTROLLER_CODE'))) {
            $return_value->setControllerCode($this->getParameter(__ApplicationContext::getInstance()->getPropertyContent('REQUEST_CONTROLLER_CODE')));
        }
        if($this->hasParameter(__ApplicationContext::getInstance()->getPropertyContent('REQUEST_ACTION_CODE'))) {
            $return_value->setActionCode($this->getParameter(__ApplicationContext::getInstance()->getPropertyContent('REQUEST_ACTION_CODE')));
        }
        return $return_value;
    }
    
    /**
     * Returns the action code (if it has been specified as a parameter) associated to
     * the current request
     *
     * @return string The requested action code
     */
    public function getActionCode() {
        $request_action_code = __ContextManager::getInstance()->getApplicationContext()->getPropertyContent('REQUEST_ACTION_CODE');
        return $this->getParameter($request_action_code);
    }

    /**
     * Returns the action code (if it has been specified as a parameter) associated to
     * the current request
     *
     * @return string The requested action code
     */
    public function getControllerCode() {
        $request_controller_code = __ContextManager::getInstance()->getApplicationContext()->getPropertyContent('REQUEST_CONTROLLER_CODE');
        return $this->getParameter($request_controller_code);
    }
    
    /**
     * Returns the submit code (if it has been specified as a parameter) associated to
     * the current request
     *
     * @todo remove this method
     * 
     * @return string The submit code
     */
    public function getSubmitCode()
    {
        return $this->getParameter(__ContextManager::getInstance()->getApplicationContext()->getPropertyContent('REQUEST_SUBMIT_CODE'));        
    }
    
    public function setRequestType($request_type) {
        $this->_request_type = $request_type;
    }
    
    public function getRequestType() {
        return $this->_request_type;
    }
    
    /**
     * Populate current instance with all values extracted from client request
     *
     */
    abstract public function readClientRequest();
    
}