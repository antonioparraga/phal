<?php

/**
 * Class representing a context container
 * 
 * The application context can be retrieved by calling the {@link __ApplicationContext::getInstance()} singleton method.
 * 
 */
class __Context {
    
	protected $_cache = null;
	
    protected $_context_id = null;
    
    protected $_uniq_code  = null;
    
    protected $_request_scope_instances = array();
    
    protected $_instance_definitions = array();
    
    protected $_request_scope_instance_definitions = array();
    
    protected $_instances_requested = array();
    
    protected $_configuration = null;
    
    protected $_configuration_loader = null;
    
    protected $_context_base_dir = null;
    
    protected $_instance_factory = null;
    
    public function __construct($context_id, $context_base_dir) {
        $this->_context_id = $context_id;
        if(is_readable($context_base_dir) && is_dir($context_base_dir)) {
            $this->_context_base_dir = $context_base_dir;
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Error trying to set unreadable or unexistent directory (' . $context_base_dir . ') as base directory for context ' . $context_id);
        }
    }
    
    public function loadConfiguration($configuration_file = null) {
        //read context configuration:
        $this->_configuration_loader = new __ConfigurationLoader($this->_context_id);
        $this->_configuration = $this->_configuration_loader->loadConfiguration($configuration_file);

        //process and classify the instance definitions:
        $instance_definitions = $this->_configuration->getSection('configuration')->getSection('peppers');
        if(is_array($instance_definitions)) {
            $this->_instance_definitions = $instance_definitions[__InstanceDefinition::SCOPE_ALL];
            $this->_request_scope_instance_definitions = $instance_definitions[__InstanceDefinition::SCOPE_REQUEST];
        }
    }
    
    /**
     * Gets the session associated to current context
     *
     * @return __Session
     */
    public function &getSession() {
        return __SessionManager::getInstance()->getSession($this->_context_id);
    }
    
    /**
     * Gets the cache associated to current context
     *
     * @return __Cache
     */
    public function &getCache() {
    	if($this->_cache == null) {
        	$this->_cache = __CacheManager::getInstance()->getCache();
    	}
    	return $this->_cache;
    }
    
    /**
     * Gets the logger associated to current context
     *
     * @return __Logger
     */
    public function &getLogger($appender = null) {
        return __LogManager::getInstance()->getLogger($this->_context_id, $appender);
    }
    
    /**
     * Get the {@link __ResourceManager} associated to current context
     *
     * @return __ResourceManager
     */
    public function &getResourceManager() {
        return __ResourceManager::getContextInstance($this->_context_id);
    }
    
    /**
     * Get a I18n {@link __Resource}
     *
     * @return __Resource
     */
    public function &getResource($resource_id, $language_iso_code = null) {
        $return_value = null;
        $resource_manager = __ResourceManager::getContextInstance($this->_context_id);
        if($resource_manager instanceof __ResourceManager) {
            $return_value = $resource_manager->getResource($resource_id, $language_iso_code);
        }
        return $return_value;
    }    
    
    /**
     * Gets the context instances factory
     * 
     * @return __InstanceFactory
     */
    public function &getInstanceFactory() {
        if($this->_instance_factory == null) {
            $this->_instance_factory = new __InstanceFactory($this);
        }
        return $this->_instance_factory;
    }
    
    /**
     * Get the current context configuration loader
     *
     * @return __ConfigurationLoader
     */
    public function &getConfigurationLoader() {
        return $this->_configuration_loader;
    }
    
    /**
     * This method is reserved for internal usage by phal.
     * 
     * Do not call
     *
     */
    public function startup() {
        //get context instances:
        $cache = $this->getCache();
        if($cache->getData('__Context::' . $this->_context_id . '::_instances_created') == null) {
            //create all non-lazy instances:
            $this->_createNonLazyInstances($this->_instance_definitions);
            $dummy = true;
            $cache->setData('__Context::' . $this->_context_id . '::_instances_created', $dummy);
        }
        else {
            //create just REQUEST scope non-lazy instances:
            $this->_createNonLazyInstances($this->_request_scope_instance_definitions);
        }
        //now initialize the authorization manager
        __AuthorizationManager::getInstance();
    }
            
    /**
     * Alias of {@link __Context::getContextId()}
     *
     * @return string the Id for current __Context instance
     */
    public function getId() {
        return $this->getContextId();
    }
    
    /**
     * Returns the Id for current __Context instance
     *
     * @return string The Id for current __Context instance
     */
    public function getContextId() {
        return $this->_context_id;
    }

    /**
     * Gets the root directory where the application represented by the context container is located at
     *
     * @return string
     */
    public function getBaseDir() {
        return $this->_context_base_dir;
    }
    
    public function setUniqCode($uniq_code) {
        $this->_uniq_code = $uniq_code;
    }
    
    public function getUniqCode() {
        return $this->_uniq_code;
    }
    
    protected function _addInstanceToCache($instance, $instance_id) {
    	$this->getCache()->setData('__Context::' . $this->_context_id . '::' . $instance_id, $instance);
    }
    
    protected function _getInstanceFromCache($instance_id) {
    	return $this->getCache()->getData('__Context::' . $this->_context_id . '::' . $instance_id);
    }
    
    protected function _createNonLazyInstances(array $instance_definitions) {
        foreach( $instance_definitions as $instance_id => &$instance_definition ) {
            //Will create just singleton instances that are not lazy:
            if(!$instance_definition->isLazy() && $instance_definition->isSingleton()) {
            	$instance = $this->_getInstanceFromCache($instance_id);
                if($instance == null) {
                    $this->_createInstance($instance_id);
                }
            }
        }
    }
    
    public function hasInstance($instance_id){
    	//also check against instance_definitions to avoid issues when the instance to check to hasn't been yet created
        if($this->_getInstanceFromCache($instance_id) != null || 
           key_exists($instance_id, $this->_request_scope_instances) ||
           key_exists($instance_id, $this->_instance_definitions)) {
            return true;
        }
        else {
        	return false;
        }
    }

    public function &getInstanceDefinition($instance_id) {
        $return_value = null;
        if(key_exists($instance_id, $this->_instance_definitions)) {
            $return_value =& $this->_instance_definitions[$instance_id];
        }
        return $return_value;
    }

    /**
     * Get an instance managed by the phal context (aka pepper)
     *
     * @deprecated use getContextInstance instead of
     *
     * @param string $instance_id
     * @return mixed
     */
    public function &getPepper($pepper_id) {
    	return $this->getInstance($pepper_id);
    }
    
	/**
	 * Alias of getPepper
	 * 
	 * @param unknown $instance_id
	 * @return mixed
	 */
    public function &getInstance($instance_id) {
        $return_value = null;
        $instance = $this->_getInstanceFromCache($instance_id);
        if($instance == null) {
	        if(key_exists($instance_id, $this->_request_scope_instances)) {
	            $instance =& $this->_request_scope_instances[$instance_id];
	        }
	        else {
	            $instance = $this->_createInstance($instance_id);
	        }
        }
        
        if(key_exists($instance_id, $this->_instance_definitions)) {
        	$instance_definition = $this->_instance_definitions[$instance_id];
            if(!$instance_definition->isSingleton()) {
	            $return_value = clone($instance);
	        }
	        else {
	            $return_value =& $instance;
	        }
        }
        else {
        	throw __ExceptionFactory::getInstance()->createException('ERR_INSTANCE_ID_NOT_FOUND', array($instance_id));
        }
        
        return $return_value;
    }
    
    protected function &_createInstance($instance_id) {
        
        $instance_definition = $this->_instance_definitions[$instance_id];
        //check if the requested instance is a resource:
        if(key_exists($instance_id, $this->_instances_requested)) {
            throw __ExceptionFactory::getInstance()->createException('ERR_CIRCULAR_DEPENDENCY_INJECTION', array($instance_id));
        }
        $this->_instances_requested[$instance_id] = true;
        $return_value = $this->getInstanceFactory()->createInstance($instance_definition);
        //do not store non-serializable instances in the instances array:
        $scope = $instance_definition->getScope();
        switch($scope) {
            case __InstanceDefinition::SCOPE_REQUEST:
                $this->_request_scope_instances[$instance_id] =& $return_value;
                break;
            default:
            	$this->_addInstanceToCache($return_value, $instance_id);
                break;
        }
        unset($this->_instances_requested[$instance_id]);

        return $return_value;
    }
    
    
    /**
     * Alias of getInstance
     * 
     * @param string $instance_id
     * @return mixed
     */
    public function &getContextInstance($instance_id) {
        $return_value = $this->getInstance($instance_id);
        return $return_value;
    }

    public function &getFlowScope() {
        return __FlowExecutor::getInstance()->getActiveFlowExecution();
    }
    
    public function &getRequestScope() {
        $return_value = __FrontController::getInstance()->getRequest();
        return $return_value;
    }
    
    public function &getSessionScope() {
        return $this->getSession();
    }    
    
    /**
     * Adds a __Configuration instance to the current configuration context.
     * It will be merged with the existent __Configuration instance
     *
     * @param __Configuration &$configuration The __Configuration to add to.
     * 
     */
    public function addConfiguration(__Configuration &$configuration) {
        if( !($this->_configuration instanceof __Configuration) ) {
            $this->_configuration =& $configuration;
        }
        else {
            $this->_configuration->merge($configuration);
        }
    }     
    
    /**
     * Get the {@link __Configuration} instance associated to the current context
     *
     * @return __Configuration The configuration instance associated to the current context
     */
    public function &getConfiguration() {
        return $this->_configuration;
    }
    
    /**
     * Retrieves a property content if defined
     *
     * @param string $property_name The name of the property
     * @return string The property content
     */
    public function getPropertyContent($property_name) {
        $return_value = null;
        if($this->_configuration != null) {
            $return_value = $this->_configuration->getPropertyContent($property_name);
        }
        return $return_value;
    }

    /**
     * Set a property content overriding existing one if any
     *
     * @param string $property_name The name of the property
     * @param string $property_value The value of the property
     * @return string The property content
     */
    public function setPropertyContent($property_name, $property_value) {
        $return_value = null;
        if($this->_configuration != null) {
            $this->_configuration->setPropertyContent($property_name, $property_value);
        }
        return $return_value;
    }

    /**
     * Check if exists a given property by name
     *
     * @param string $property_name
     * @return bool
     */
    public function hasProperty($property_name) {
        $return_value = false;
        if($this->_configuration != null) {
            $return_value = $this->_configuration->hasProperty($property_name);
        }
        return $return_value;
    }       

}