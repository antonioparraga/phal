<?php


abstract class __FrontController implements __IFrontController {

    protected static $_instance = null;
    protected $_request  = null;
    protected $_response = null;
    
    public function &getRequest() {
        return $this->_request;
    }

    public function &getResponse() {
        return $this->_response;
    }
    
    /**
     * Singleton method to retrieve the __FrontController instance
     *
     * @return __FrontController The singleton __FrontController instance
     */
    final public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = self::_createFrontController();
        }
        return self::$_instance;
    }
    
    /**
     * Factory method for creating a __FrontController to dispatch the client request
     *
     * @return __IFrontController
     */
    final private static function _createFrontController() {
        $client_request = __Client::getInstance()->getRequest();
        //if the client request has been initialized successfully, will ask for correspondent front controller:
        if($client_request != null) {
            $front_controller_class = $client_request->getFrontControllerClass();
        }
        //otherwise will get the most appropriate fron controller depending on the client type (http, commandline, ...):
        else {
            $front_controller_class = __Client::getInstance()->getDefaultFrontControllerClass();
        }
        $front_controller = new $front_controller_class();
        if(! $front_controller instanceof __IFrontController ) {
            throw __ExceptionFactory::getInstance()->createException('ERR_WRONG_FRONT_CONTROLLER_CLASS', array($front_controller_class));
        }
        return $front_controller;
    }

    public function dispatchClientRequest() {
        //map the request/response with the client
        $request  = __Client::getInstance()->getRequest();
        $response = __Client::getInstance()->getResponse();
        //call to dispatch the request
        $this->dispatch($request, $response);
        //output whatever pending (buffered) content in the response
        $response->flush();
    }
        
    public function dispatch(__IRequest &$request, __IResponse &$response) {
        //set the current request and response:
        $this->_request  =& $request;
        $this->_response =& $response;
        //dispatch the request:
        if($request->hasFilterChain()) {
            $filter_chain = $request->getFilterChain();
            $filter_chain->reset();
            $filter_chain->setFrontControllerCallback($this, 'processRequest');
            $filter_chain->execute($request, $response);
        }
        else {
            $this->processRequest($request, $response);
        }
    }
    
    public function getRequestType() {
        if($this->_request != null) {
            return $this->_request->getRequestType();
        }
        else {
            return __Client::getInstance()->getRequestType();
        }
    }
    
    abstract public function processRequest(__IRequest &$request, __IResponse &$response);
    
}
