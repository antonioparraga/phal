<?php

class __ExceptionFactory {
    
    static private $_instance = null;
    private $_error_table = null;
    
    /**
     * Constructor method
     */
    private function __construct()
    {
        $configuration = __ContextManager::getInstance()->getCurrentContext()->getConfiguration();
        if($configuration != null) {
            $configuration_section = __ContextManager::getInstance()->getCurrentContext()->getConfiguration()->getSection('configuration');
            if($configuration_section != null) {
                $this->_error_table = $configuration_section->getSection('errors');
            }
        }
    }
    
    /**
     * This method return a singleton instance of __ExceptionFactory
     *
     * @return __ExceptionFactory A singleton reference to the __ExceptionFactory
     */
    static public function &getInstance()
    {
        if (self::$_instance == null) {
            // Use "Lazy initialization"
            self::$_instance = new __ExceptionFactory();
        }
        return self::$_instance;
    }
    
    public function &getErrorTable() {
        return $this->_error_table;
    }    
    
    /**
     * Creates a new __PhalException children instance (depending on the error_id parameter).
     *
     * @param string $error_id
     * @param mixed $parameters Error message parameters
     * @return __PhalException
     */
    public function &createException($error_id, $parameters = array()) {
        if(!is_array($parameters)) {
            $parameters = array($parameters);
        }
        if($this->_error_table != null) {
            if(is_numeric($error_id)) {
                $error_code = $error_id;
                $error_id   = $this->_error_table->getErrorId($error_code);
            }
            else {
                $error_code = $this->_error_table->getErrorCode($error_id);
            }
            if($error_code != null) {
                $exception_class = $this->_error_table->getExceptionClass($error_code);
                $error_message   = $this->_error_table->getErrorMessage($error_code, $parameters);
                $return_value = new $exception_class($error_message, $error_code);
            }
            else {
                $error_code = 0;
                if(__ResourceManager::getInstance()->hasResource($error_id)) {
                    $error_message = __ResourceManager::getInstance()->getResource($error_id)->setParameters($parameters)->getValue();
                }
                else {
                    $error_message = $error_id;                    
                }
                $return_value  = new __UnknowException($error_message, $error_code);
            }
        }
        else {
            $return_value = new __UnknowException($error_id, 0);
        }
        if( $return_value instanceof __PhalException ) {
            $return_value->setErrorMessageResourceId($error_id);
            $return_value->setErrorMessageParameters($parameters);
            if( $this->_error_table != null ) {
                $return_value->setErrorTitle( $this->_error_table->getErrorTitle($error_code) );
            }
        }
        return $return_value;
    }
    
}