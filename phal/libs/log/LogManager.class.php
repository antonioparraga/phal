<?php

class __LogManager {
    
    private static $_instance = null;
    private $_loggers = array();
    
    const DEFAULT_LOGGER_CLASS = '__DummyLogger';
    const DEFAULT_LOG_ID = 'default';
    const DEFAULT_APPENDER = 'default';
    
    public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __LogManager();
        }
        return self::$_instance;
    }
    
    public function &getLogger($log_id = null, $appender = null) {
        if(empty($log_id)) {
            $log_id = __LogManager::DEFAULT_LOG_ID;
        }
        if(!key_exists($log_id, $this->_loggers)) {
            $this->_loggers[$log_id] = $this->_createLoggerInstance($log_id);
        }
        return $this->_loggers[$log_id];
    }
    
    private function &_createLoggerInstance($log_id) {
        $logger_class = __LogManager::DEFAULT_LOGGER_CLASS; //by default
        $phal_runtime_directives = __Phal::getInstance()->getRuntimeDirectives();
        if($phal_runtime_directives->hasDirective('LOG_ENABLED') && 
           $phal_runtime_directives->getDirective('LOG_ENABLED')) {
            if($phal_runtime_directives->hasDirective('LOGGER_CLASS')) {
                $logger_class = $phal_runtime_directives->getDirective('LOGGER_CLASS');            
            }        
        }
        if(class_exists($logger_class)) {
            $logger = new $logger_class($log_id);
        }
        else {
            throw new __ConfigurationException('Class not found: ' . $logger_class);
        }
        return $logger;
    }
    
}