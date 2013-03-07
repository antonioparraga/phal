<?php


/**
 * This is the class in charge of manage I18n resources, being text, error messages, images an so on.
 * 
 * The application context has his own resource manager, which can be retrieved by calling the {@link __Context::getResourceManager()} method
 * 
 * <code>
 * //Retrieve the __ResourceManager instance associated to the application context:
 * $resource_manager = __ApplicationContext::getInstance()->getResourceManager();
 * 
 * </code>
 * <br>
 */
class __ResourceManager {
    
    private $_context_id = null;
    
    static private $_instance = null;
    
    static private $_context_instances = array();
    
    private $_loaded_action_codes = array();
    
    /**
     * This is the hash that will store all {@link __ResourceProviders} instances handled by the __ResourceManager
     *
     * @var array
     */
    private $_resource_providers = array( PERSISTENCE_LEVEL_SESSION => array(),
                                          PERSISTENCE_LEVEL_ACTION  => array() );
    
    /**
     * This is the table where all the resources will be stored into
     *
     * @var unknown_type
     */
    private $_resource_table = null;

    /**
     * This is the constructor method. It will load all resource providers used by the __ResourceManager to serve Resources
     */
    private function __construct($context_id = null) {
        if($context_id == null) {
            $context_id = __CurrentContext::getInstance()->getContextId();
        }
        $this->_context_id = $context_id;
        $context = __ContextManager::getInstance()->getContext($context_id);
        $cache = $context->getCache();
        $language_iso_code = __I18n::getInstance()->getLocale()->getLanguageIsoCode();
        $language_resources_key = '__ResourceManager__' . $language_iso_code;
        $resource_manager_data = $cache->getData($language_resources_key);
        if($resource_manager_data == null) {
            //initialize the resource table:
            $this->_resource_table = new __ResourceTable();
            //load resource dictionaries:
            $resource_providers = $context->getConfiguration()->getSection('configuration')->getSection('resource-providers');
            if(is_array($resource_providers)) {
                $this->_resource_providers = &$resource_providers;
            }
            //load session resources:
            $this->loadResources($language_iso_code);
            
            //store in cache all variables:
            $resource_manager_data = array('resource_table'      => $this->_resource_table,      
                                           'resource_providers'  => $this->_resource_providers);
            $cache->setData($language_resources_key, $resource_manager_data);
        }
        else {
            $this->_resource_table      = $resource_manager_data['resource_table'];
            $this->_resource_providers  = $resource_manager_data['resource_providers'];
        }
    }
    
    /**
     * Maintained for back-compatibility, but deprecated.
     * Use {@link __Context::getResourceManager()} method instead of
     *
     * @return __ResourceManager
     * @deprecated Use {@link __Context::getResourceManager()} method instead of
     */
    static public function &getInstance() {
        $return_value = __CurrentContext::getInstance()->getResourceManager();
        return $return_value;
    }
    
    /**
     * This method return a singleton instance of __ResourceManager associated to a context id
     *
     * @return __ResourceManager
     */
    static public function &getContextInstance($context_id) {
        if (!key_exists($context_id, self::$_context_instances)) {
            // Use "Lazy initialization"
            self::$_context_instances[$context_id] = new __ResourceManager($context_id);
        }
        return self::$_context_instances[$context_id];
    }
    
    public function loadActionResources(__ActionIdentity $action_identity, $language_iso_code = null) {
        if($language_iso_code == null) {
            $language_iso_code = __I18n::getInstance()->getLocale()->getLanguageIsoCode();
        }
        $controller_code = $action_identity->getControllerCode();
        $cache = __ApplicationContext::getInstance()->getCache();
        $action_resources_key = '__ActionResources__' . $language_iso_code . '__' . $controller_code;
        $action_resources = $cache->getData($action_resources_key);
        if($action_resources == null) {
            $action_resources = array();
            $action_controller_definition = __ActionControllerResolver::getInstance()->getActionControllerDefinition($controller_code);
            $I18n_resource_groups = $action_controller_definition->getI18nResourceGroups();
            if(count($I18n_resource_groups) == 0) {
                $I18n_resource_groups[] = $controller_code;
            }
            foreach($I18n_resource_groups as $I18n_resource_group) {
                $resources_group_to_load = new __ActionIdentity($I18n_resource_group);
                foreach($this->_resource_providers[PERSISTENCE_LEVEL_ACTION] as &$resource_provider) {
                    $action_resources = $resource_provider->loadResources($language_iso_code, $resources_group_to_load) + $action_resources;
                }
            }
            $cache->setData($action_resources_key, $action_resources);
        }
        if(!key_exists($controller_code, $this->_loaded_action_codes)) {
            $this->_resource_table->addActionResources($action_resources, $action_identity, $language_iso_code);
            $this->_loaded_action_codes[$controller_code] = true;
        }
        return $action_resources;
    }    
    
    /**
     * This method loads all the session level resources for an specific language.
     * Note that action specific resources are loaded dinamically by each resource dictionary
     *
     * @param string The language iso code to load resources from
     */
    private function loadResources($language_iso_code = null) {
        $context = __ContextManager::getInstance()->getContext($this->_context_id);
        if($language_iso_code == null) {
            $language_iso_code = __I18n::getInstance()->getLocale()->getLanguageIsoCode();
        }
        if($this->_resource_table->hasLanguage($language_iso_code) == false) {
            //Now will iterate throught the Resources, appending all in the return array
            foreach($this->_resource_providers[PERSISTENCE_LEVEL_SESSION] as &$resource_provider) {
                $this->_resource_table->addResources($resource_provider->loadResources($language_iso_code), $language_iso_code);
            }
        }
    }
    
    /**
     * This method append a new {@link __ResourceDictionary} instance.
     * __ResourceDictionary are objects that group Resources
     *
     * @param __ResourceDictionary The resource dictionary to add to
     */
    public function addResourceProvider(__ResourceProvider &$resource_provider) {
        $persistence_level = $resource_provider->getPersistenceLevel();
        if(!in_array($resource_provider, $this->_resource_providers[$persistence_level])) {
            $this->_resource_providers[$persistence_level][] =& $resource_provider;
        }
    }
       
    /**
     * This method will returns a resource identified by a key.
     * It will ask to all {@link __ResourceDictionary} instances if any of them has the resource, and will return it if it's found.
     *
     * @param string The resource's key
     * 
     * @return __ResourceBase The requested resource or null if it's not found.
     * 
     */
    public function getResource($resource_key, $language_iso_code = null) {
        $return_value = null;
        if($language_iso_code == null) {
            $language_iso_code = __I18n::getInstance()->getLocale()->getLanguageIsoCode();
        }
        if($this->_resource_table->hasLanguage($language_iso_code) == false) {
            $this->loadResources($language_iso_code);
        }
        return $this->_resource_table->getResource($resource_key, $language_iso_code);
    }

    public function hasResource($resource_key, $language_iso_code = null) {
        if($language_iso_code == null) {
            $language_iso_code = __I18n::getInstance()->getLocale()->getLanguageIsoCode();
        }
        if($this->_resource_table->hasLanguage($language_iso_code) == false) {
            $this->loadResources($language_iso_code);
        }
        return $this->_resource_table->hasResource($resource_key, $language_iso_code);
    }
    
    public function addResource(__ResourceBase &$resource, $language_iso_code = null) {
        if($language_iso_code == null) {
            $language_iso_code = __I18n::getInstance()->getLocale()->getLanguageIsoCode();
        }
        $this->_resource_table->addResource($resource, $language_iso_code);
    }
    
    public function removeResource($resource_id) {
        $this->_resource_table->removeResource($resource_id);
    }
    
}



