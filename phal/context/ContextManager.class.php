<?php

/**
 * This class is in charge of {@link __Context} management.
 *
 */
class __ContextManager {
    
    static private $_instance = null;
    
    protected $_current_context_id = null;
    protected $_application_context_id = null;
    
    protected $_contexts = array();
    
    private function __construct() {
        $this->_application_context_id = strtoupper(__Phal::getInstance()->getRuntimeDirectives()->getDirective('APP_NAME'));
    }
    
    /**
     * Returns the singleton instance of __ContextManager class
     *
     * @return __ContextManager The singleton instance of __ContextManager class
     */
    static public function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __ContextManager();
        }
        return self::$_instance;
    }    
    
    public function isApplicationContextCreated() {
        return key_exists($this->_application_context_id, $this->_contexts);
    }
    
    public function createApplicationContext() {
        if(defined('APP_CONFIG_FILE')) {
            $config_file = APP_CONFIG_FILE;
        }
        else {
            $config_file = null;
        }
        $this->createContext($this->_application_context_id, APP_DIR, $config_file);
    }    
    
    public function switchContext($context_id) {
        if($this->hasContext($context_id)) {
            $this->_current_context_id = $context_id;
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_CONTEXT_NOT_FOUND', array($context_id));
        }
    }

    /**
     * Returns the id of the active {@link __Context} instance
     *
     * @return string The id of the active context
     */
    public function getCurrentContextId() {
        return $this->_current_context_id;
    }
    
    /**
     * Returns the active {@link __Context} instance
     *
     * @return __Context The active context
     */
    public function &getCurrentContext() {
        return $this->getContext($this->_current_context_id);
    }
    
    /**
     * Returns the application {@link __Context} instance
     *
     * @return __Context The application context
     */
    public function &getApplicationContext() {
        return $this->getContext($this->_application_context_id);
    }
    
    public function hasContext($context_id) {
        return key_exists($context_id, $this->_contexts);
    }
    
    /**
     * Returns the {@link __Context} for specified id, or null if not exist
     *
     * @param string $context_id The id of the context to retrieve
     * @return __Context The context for specified id
     */
    public function &getContext($context_id) {
        $return_value = null;
        $context_id = strtoupper($context_id);
        if(key_exists($context_id, $this->_contexts)) {
            $return_value =& $this->_contexts[$context_id];
        }
        return $return_value;
    }

    public function &createContext($context_id, $context_base_dir, $configuration_file = null) {
        if($this->hasContext($context_id)) {
            throw __ExceptionFactory::getInstance()->createException('Context already exists for context identifier ' . $context_id);
        }
        
        //create a class file locator for the context to create to
        $class_file_locator = new __ClassFileLocator($context_base_dir);
        __ClassLoader::getInstance()->addClassFileLocator($class_file_locator);
        //scann in depth starting from configuration location before scanning the context basedir
        if($configuration_file != null) {
            $configuration_base_dir = dirname($configuration_file);
            if(strpos($configuration_base_dir, $context_base_dir) === false) {
                $configuration_class_file_locator = new __ClassFileLocator($configuration_base_dir);
                __ClassLoader::getInstance()->addClassFileLocator($configuration_class_file_locator);
            }
        }
        
        //do not read nor store into the cache the initial context in case of DEBUG_MODE active:
        if(__Phal::getInstance()->getRuntimeDirectives()->getDirective('DEBUG_MODE')) {
            $cache   = null; //by default
            $context = null;
        } 
        else {
            $cache = __CacheManager::getInstance()->getCache();
            $context = $cache->getData('__Context__' . $context_id);
            if($context != null) {
                $this->_addContext($context);
            }            
        }
        //if no context has been read from cache:
        if($context == null) {
            $context = new __Context($context_id, $context_base_dir);
            $this->_addContext($context);
            $context->loadConfiguration($configuration_file);
            if($cache != null) {  
                $cache->setData('__Context__' . $context_id, $context);
            }
        }
        //Startup the context
        $context->startup();
        
        //return a reference to the already created context:
        return $context;
    }

    protected function _addContext(__Context &$context) {
        $context_id = $context->getContextId();
        $this->_contexts[$context_id] =& $context;
        //Switch to the new context
        $this->switchContext($context_id);
    }
    
}