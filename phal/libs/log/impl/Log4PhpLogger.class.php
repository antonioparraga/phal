<?php

if (!defined('LOG4PHP_DIR')) define('LOG4PHP_DIR', PHAL_DIR . '/libs/thrdparty/log4php');

class __Log4PhpLogger implements __ILogger {
    
    private $_log4php_instance = null;
    
    const DEFAULT_CONFIGURATION_TYPE = 'PROPERTIES';
    
    public function __construct($log_id) {
        $this->_loadLog4PhpConf($log_id);
    }
    
    private function _loadLog4PhpConf($log_id) {
        $phal_runtime_directives = __Phal::getInstance()->getRuntimeDirectives();
        if(!defined('LOG4PHP_CONFIGURATION') && 
           $phal_runtime_directives->hasDirective('LOG4PHP_CONFIG_FILE')) {
            $log4php_config_file = __PathResolver::resolvePath($phal_runtime_directives->getDirective('LOG4PHP_CONFIG_FILE'));
            define('LOG4PHP_CONFIGURATION', $log4php_config_file);
        }
        if(!defined('LOG4PHP_CONFIGURATOR_CLASS')) {
            if($phal_runtime_directives->hasDirective('LOG4PHP_CONFIGURATION_TYPE')) {
                $configuration_type = $phal_runtime_directives->getDirective('LOG4PHP_CONFIGURATION_TYPE');
            }
            else {
                $configuration_type = __Log4PhpLogger::DEFAULT_CONFIGURATION_TYPE;
            }
            switch(strtoupper($configuration_type)) {
                case 'XML':
                    define('LOG4PHP_CONFIGURATOR_CLASS', LOG4PHP_DIR.'/xml/LoggerDOMConfigurator');
                    break;
            }
        }
        $this->_log4php_instance = LoggerManager::getLogger($log_id);
    }
    
    public function debug($string) {
        $this->_log4php_instance->debug($string);
    }

    public function info($string) {
        $this->_log4php_instance->info($string);
    }
    
    public function warn($string) {
        $this->_log4php_instance->warn($string);
    }

    public function error($string) {
        $this->_log4php_instance->error($string);
    }
    
    public function fatal($string) {
        $this->_log4php_instance->fatal($string);
    }
    
}
