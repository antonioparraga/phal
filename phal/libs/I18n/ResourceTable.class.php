<?php


/**
 * This class is an utility class for __ResourceManager class, that will be the class in charge of contain all the resource dictionaries
 * that are handled by the __ResourceManager instance.
 * 
 */
class __ResourceTable {

    /**
     * This is the array that contains all the resources, classified by languages
     *
     * @var array
     */
    private $_resources = array();

    /**
     * This method add a set of resources to the _resources internal array
     *
     * @param array $resources Resources to add to
     */
    public function addResources(array $resources, $language_iso_code) {
        $language_iso_code = strtoupper($language_iso_code);
        if(!$this->hasLanguage($language_iso_code)) {
            $this->addLanguage($language_iso_code);
        }
        $this->_resources[$language_iso_code] += $resources;
    }

    public function addResource($resource, $language_iso_code) {
        $language_iso_code = strtoupper($language_iso_code);
        $resource_key = $resource->getKey();
        if(!$this->hasLanguage($language_iso_code)) {
            $this->addLanguage($language_iso_code);
        }
        $this->_resources[$language_iso_code][$resource_key] = $resource;
    }
    
    public function addActionResources(array $resources, __ActionIdentity $action_identity, $language_iso_code) {
        $language_iso_code = strtoupper($language_iso_code);
        if(!$this->hasLanguage($language_iso_code)) {
            $this->addLanguage($language_iso_code);
        }
        $this->_resources[$language_iso_code] = $resources + $this->_resources[$language_iso_code];
    }

    public function addLanguage($language_iso_code) {
        $language_iso_code = strtoupper($language_iso_code);
        if(!$this->hasLanguage($language_iso_code)) {
            $this->_resources[$language_iso_code] = array();
        }
    }

    public function unloadResources() {
        unset($this->_resources);
        $this->_resources = array();
    }

    public function hasLanguage($language_iso_code) {
        $language_iso_code = strtoupper($language_iso_code);
        return key_exists($language_iso_code, $this->_resources);
    }

    public function hasResource($resource_id, $language_iso_code) {
        $language_iso_code = strtoupper($language_iso_code);
        $return_value = false;
        if(key_exists($language_iso_code, $this->_resources) && key_exists($resource_id, $this->_resources[$language_iso_code])) {
            $return_value = true;
        }
        return $return_value;
    }

    /**
     * This method returns a concrete resource identified by an id.
     *
     * @param string $resource_id The id of the resource to load to
     * @param string $language_iso_code The language that you want to retrieve the resource from. If omitted, the current one will be taken from the execution
     * @return __ResourceBase The requested resource
     */
    public function getResource($resource_id, $language_iso_code) {
        $language_iso_code = strtoupper($language_iso_code);
        $return_value = null;
        if(key_exists($language_iso_code, $this->_resources) && key_exists($resource_id, $this->_resources[$language_iso_code])) {
            $return_value = $this->_resources[$language_iso_code][$resource_id];
        }
        else {
            $default_resource_class = __ApplicationContext::getInstance()->getPropertyContent('DEFAULT_RESOURCE_CLASS');
            $return_value = new $default_resource_class();
            $return_value->setKey($resource_id);
            $return_value->setValue("??[" . $resource_id . "]??");            
        }
        return $return_value;
    }


}





