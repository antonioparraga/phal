<?php

class __ExecutionException extends __PhalException{
    protected $_exception_type = __ExceptionType::NOTICE ;
   
    public function getLogLevel() {
        return __LogLevel::NOT_LOGABLE;        
    }
    
}
