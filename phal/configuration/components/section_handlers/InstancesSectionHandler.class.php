<?php

/**
 * This is the section handler in charge of processing &lt;pepperss&gt; configuration sections
 *
 */
class __InstancesSectionHandler extends __CacheSectionHandler {
    
    private $_instance_definitions = null;
    
    public function &doProcess(__ConfigurationSection &$section) {
        unset($this->_instance_definitions);
        $this->_instance_definitions = array(__InstanceDefinition::SCOPE_ALL     => array(),
                                             __InstanceDefinition::SCOPE_REQUEST => array());
        $sections = $section->getSections();
        foreach($sections as &$section) {
            if($section->getName() == 'pepper') {
                $instance_definition =& $this->_createInstanceDefinition($section);
                $this->_addInstanceDefinition($instance_definition);
            }
        }
        return $this->_instance_definitions;
    }

    private function _addInstanceDefinition(__InstanceDefinition &$instance_definition) {
        if($instance_definition->validate()) {
            $instance_id = $instance_definition->getId();
            if(!key_exists($instance_id, $this->_instance_definitions)) {
                $this->_instance_definitions[__InstanceDefinition::SCOPE_ALL][$instance_id] =& $instance_definition;
                $this->_instance_definitions[$instance_definition->getScope()][$instance_id] =& $instance_definition;
            }
            else {
                throw new __ConfigurationException('Double definition of instance "' . $instance_id . '"');
            }
        }
    }    
    
    /**
     * create a new instance according to the instance definition
     *
     * @param __ConfigurationSection $instance_definition
     */
    private function &_createInstanceDefinition(__ConfigurationSection $instance_config_section) {
        if($instance_config_section->hasAttribute('id')) {
            $instance_id = $instance_config_section->getAttribute('id');
        }
        else {
            $instance_id = uniqid();
        }
        $instance_class = $instance_config_section->getAttribute('class');
        $instance_definition = new __InstanceDefinition($instance_id);
        $instance_definition->setClass($instance_class);
        if($instance_config_section->hasAttribute('singleton')) {
            $instance_definition->setSingleton($instance_config_section->getAttribute('singleton'));
        }
        if($instance_config_section->hasAttribute('scope')) {
            $instance_definition->setScope($instance_config_section->getAttribute('scope'));
        }
        
        if($instance_config_section->hasAttribute('lazy-init')) {
            $instance_definition->setLazy(__ConfigurationValueResolver::toBool($instance_config_section->getAttribute('lazy-init')));
        }
        else if($instance_config_section->hasAttribute('lazy')) {
            $instance_definition->setLazy(__ConfigurationValueResolver::toBool($instance_config_section->getAttribute('lazy')));
        }
        
        if($instance_config_section->hasAttribute('startup-method')) {
            $instance_definition->setStartupMethod($instance_config_section->getAttribute('startup-method'));
        }
        if($instance_config_section->hasAttribute('shutdown-method')) {
            $instance_definition->setShutdownMethod($instance_config_section->getAttribute('shutdown-method'));
        }
        if($instance_config_section->hasAttribute('factory-method')) {
            $instance_definition->setFactoryMethod($instance_config_section->getAttribute('factory-method'));
        }
        if($instance_config_section->hasAttribute('factory-instance')) {
            $instance_definition->setFactoryInstanceId($instance_config_section->getAttribute('factory-instance'));
        }
        $properties = $instance_config_section->getSections();
        foreach($properties as &$property) {
            switch($property->getName()) {
                case 'property':
                    $this->_parseProperty($property, $instance_definition);
                    break;
                case 'constructor-arg':
                    $this->_parseConstructorArgument($property, $instance_definition);
                    break;
            }
        }
        return $instance_definition;
    }
    
    private function _parseProperty(__ConfigurationSection $property, __InstanceDefinition &$instance_definition) {
        $property_name  = $property->getAttribute('name');   
        $property_value = $this->_parsePropertyValue($property, $property_name, $instance_definition->getId());
        $instance_definition->addProperty($property_name, $property_value); 
    }
    
    private function _parseConstructorArgument(__ConfigurationSection $property, __InstanceDefinition &$instance_definition) {
        if($property->hasAttribute('index')) {
            $argument_index = $property->getAttribute('index');
        }
        else {
            $argument_index = null;
        }
        $argument_value = $this->_parsePropertyValue($property, 'constructor argument', $instance_definition->getId());
        $instance_definition->addConstructorArgument($argument_value, $argument_index); 
    }    
    
    private function _parsePropertyValue(__ConfigurationSection $property, $property_name, $instance_id) {
        if($property->hasAttribute('value')) {
            $property_value = $property->getAttribute('value');
        }
        else if($property->hasAttribute('ref')) {
            $property_value = new __InstanceReference();
            $property_value->setReferenceId($property->getAttribute('ref'));
        }
        else {
            $value_section = $property->getSections();
            if(count($value_section) == 1) {
                $value = current($value_section);
                $property_value = $this->_parseValueNode($value, $property_name, $instance_id);
            }
            else {
                if(empty($instance_id)) {
                    $instance_id = "definition without id";
                }
                throw new __ConfigurationException("Wrong value definition for property " . $property_name . " on instance " . $instance_id);
            }
        }
        return $property_value;        
    }
    
    private function _parseValueNode(__ConfigurationSection $node_value, $property_name, $instance_id) {
        $return_value = null;
        if($node_value->getName() == 'ref') {
            if($node_value->hasAttribute('id')) {
                $return_value = new __InstanceReference();
                $return_value->setReferenceId($node_value->getAttribute('id'));
            }
        }
        else if($node_value->getName() == 'pepper') {        		
            $return_value = $this->_createInstanceDefinition($node_value);
            $this->_addInstanceDefinition($return_value);
        }
        else if($node_value->getName() == 'list') {
            $return_value = array();
            $value_nodes = $node_value->getSections();
            foreach($value_nodes as $value_node) {
                $key = null;
                if($value_node->getName() == 'entry') {
                    if($value_node->hasAttribute('key')) {
                        $key = $value_node->getAttribute('key');
                    }
                    $value_section = $value_node->getSections();
                    if(count($value_section) == 1) {
                        $value_node = current($value_section);
                    }
                    else {
                        throw new __ConfigurationException("Wrong entry content for property " . $property_name . " on instance " . $instance_id);
                    }
                }
                $value = $this->_parseValueNode($value_node, $property_name, $instance_id);
                if($key !== null) {
                    $return_value[$key] = $value;
                }
                else {
                    $return_value[] = $value;
                }
            }
        }
        else if($node_value->getName() == 'value') {
            $return_value = $this->_getPlainTextContent($node_value);
        }
        else if($node_value->getName() == 'null') {
            $return_value = null;
        }
        return $return_value;
        
    }
    
    protected function _getPlainTextContent(__ConfigurationSection &$section) {
        $value_content = $section->getProperty('#text');
        if($value_content == null) {
            $value_content = $section->getProperty('#cdata-section');
        }
        if($value_content != null) {
            $return_value = $value_content->getContent();
        }
        else {
            $return_value = '';
        }
        return $return_value;
    }
    

}