<?php


class __RequestFactory {
    
    static private $_instance       = null;

    /**
     * This is the class constructor:
     */
    private function __construct()
    {
    }

    /**
     * This method return a singleton instance of __RequestFactory
     *
     * @return __RequestFactory A singleton reference to the __RequestFactory
     */
    static public function &getInstance()
    {
        if (self::$_instance == null) {
            // Use "Lazy initialization"
            self::$_instance = new __RequestFactory();
        }
        return self::$_instance;
    }
    
    public function &createRequest($request_type = null ) {
        $return_value = null;
        if($request_type == null) {
            $request_type = __Client::getInstance()->getRequestType();
        }
        switch ($request_type) {
            case REQUEST_TYPE_COMMAND_LINE:
                $request_class = __CurrentContext::getInstance()->getPropertyContent('COMMAND_LINE_REQUEST_CLASS');
                break;
            default:
                $request_class = __CurrentContext::getInstance()->getPropertyContent('HTTP_REQUEST_CLASS');
                break;
        }
        if(class_exists($request_class)) {
            $return_value = new $request_class();
            $return_value->setRequestType($request_type);
        }
        if(!($return_value instanceof __IRequest)) {
            __ExceptionFactory::getInstance()->createException('Wrong request class: ' . $request_class . '. The request class must implement the __IRequest');
        }
        return $return_value;
    }
    
}