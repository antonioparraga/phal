<?php

class __ResponseFactory {
    
    static private $_instance       = null;

    /**
     * This is the class constructor:
     */
    private function __construct()
    {
    }

    /**
     * This method return a singleton instance of __ResponseFactory
     *
     * @return __ResponseFactory A singleton reference to the __ResponseFactory
     */
    static public function &getInstance()
    {
        if (self::$_instance == null) {
            // Use "Lazy initialization"
            self::$_instance = new __ResponseFactory();
        }
        return self::$_instance;
    }
    
    public function &createResponse($request_type = null ) {
        $return_value = null;
        if($request_type == null) {
            $request_type = __Client::getInstance()->getRequestType();
        }
        switch ($request_type) {
            case REQUEST_TYPE_COMMAND_LINE:
                $response_class = __CurrentContext::getInstance()->getPropertyContent('COMMAND_LINE_RESPONSE_CLASS');
                break;
            case REQUEST_TYPE_XMLHTTP:
                $response_class = __CurrentContext::getInstance()->getPropertyContent('XML_HTTP_RESPONSE_CLASS');
                break;
            default:
                $response_class = __CurrentContext::getInstance()->getPropertyContent('HTTP_RESPONSE_CLASS');
                break;
        }
        if(class_exists($response_class)) {
            $return_value = new $response_class();
        }
        if(!($return_value instanceof __IResponse)) {
            __ExceptionFactory::getInstance()->createException('Wrong response class: ' . $response_class . '. The response class must implement the __IResponse');
        }
        return $return_value;
    }
    
}