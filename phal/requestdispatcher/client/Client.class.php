<?php

/**
 * This class represents the client connection.
 * It contains some methods to retrieve client specific information, like the
 * client {@link __Request}, ...
 *
 */
class __Client {
    
    static private $_instance = null;
    
    private $_request_type = null;
    private $_request  = null;
    private $_response = null;
    
    /**
     * This is the class constructor:
     */
    private function __construct()
    {
    }

    /**
     * This method return a singleton instance of __Client
     *
     * @return __Client A singleton reference to the __Client
     */
    static public function &getInstance()
    {
        if (self::$_instance == null) {
            // Use "Lazy initialization"
            self::$_instance = new __Client();
        }
        return self::$_instance;
    }    
    
    public function getRequestType() {
        if($this->_request_type == null) {
            if(!empty($_SERVER['REQUEST_URI'])) {
                if((key_exists('X_REQUESTED_WITH', $_SERVER) && $_SERVER['X_REQUESTED_WITH'] == 'XMLHttpRequest') ||
                   (key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
                    $this->_request_type = REQUEST_TYPE_XMLHTTP;
                }
                else {
                    $this->_request_type = REQUEST_TYPE_HTTP;
                }
            }
            else {
                $this->_request_type = REQUEST_TYPE_COMMAND_LINE;
            }
        }
        return $this->_request_type;
    }
    
    public function getDefaultFrontControllerClass() {
        $request_type = $this->getRequestType();
        switch($request_type) {
            case REQUEST_TYPE_XMLHTTP:
                $return_value = '__AjaxFrontController';
                break;
            case REQUEST_TYPE_COMMAND_LINE:
                $return_value = '__CommandLineFrontController';
                break;
            case REQUEST_TYPE_HTTP:
            default:
                $return_value = '__HttpFrontController';
                break;
        }
        return $return_value;
    }
    
    public function &getRequest() {
        if($this->_request == null) {
            $request = __RequestFactory::getInstance()->createRequest();
            if($request instanceof __IRequest) {
                $this->_request =& $request;
                $this->_request->readClientRequest();
            }
        }
        return $this->_request;
    }
    
    public function &getResponse() {
        if($this->_response == null) {
            $response = __ResponseFactory::getInstance()->createResponse();
            if($response instanceof __IResponse) {
                if($response instanceof __HttpResponse ) {
                    //the client response will control the output buffer:
                    $response->setBufferControl(true);
                }
                $this->_response =& $response;
            }
        }
        return $this->_response;
    }
    
    
}