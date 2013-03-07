<?php

/**
 * Represents a component's property as a server end-point
 * 
 * @see __IServerEndPoint, __IEndPoint, __UIBinding
 *
 */
class __ComponentProperty extends __ServerEndPoint implements __IValueHolder {
    
    protected $_property = null;
    protected $_binding_value = null;
    protected $_mapping_values = array();
    protected $_bound_direction = __IEndPoint::BIND_DIRECTION_ALL;    
    
    /**
     * Constructor method
     *
     * @param __IComponent $component The component associated to this end-point
     * @param string $property The component property
     */
    public function __construct(__IComponent &$component, $property, $binding_value = null) {
        $this->setComponent($component);
        $this->setProperty($property);
        $this->setBindingValue($binding_value);
    }
    
    public function addMappingValue($component_value, $mapping_value) {
        $this->_mapping_values[$component_value] = $mapping_value;
    }
    
    public function setBindingValue($binding_value) {
        $this->_binding_value = $binding_value;
    }
    
    public function getBindingValue() {
        return $this->_binding_value;
    }
    
    public function unsetValue() {
        $this->_binding_value = null;
    }
    
    /**
     * Sets the property
     *
     * @param string $property The property
     */
    public function setProperty($property) {
        $this->_property = $property;
    }
    
    /**
     * Gets the property
     *
     * @return string
     */
    public function getProperty() {
        return $this->_property;
    }

    /**
     * Gets the bound direction allowed by this end-point
     *
     * @return integer
     */
    public function getBoundDirection() {
        $return_value = $this->_bound_direction;
        if($this->_binding_value !== null && $this->_binding_value !== $this->getValue()) {
            $return_value = $return_value & __IEndPoint::BIND_DIRECTION_C2S;
        }
        return $return_value;
    }
    
    /**
     * Sets a value to this end-point. 
     * 
     * If the value is different to the current one, will set the new value to the client end-point.
     *
     * @param mixed $value The value to set to
     */
    public function setValue($value) {
        if($this->_updateComponentProperty($value)) {
            $this->_ui_binding->synchronizeClient();
        }
    }
    
    public function reset() {
        $this->setValue(null);
    }
    
    /**
     * Gets the value associated to current end-point
     *
     * @todo add mapping values capability (translation rules for values from server to client end-point)
     * 
     * @return mixed
     */
    public function getValue() {
        $return_value = null;
        $component = $this->getComponent();
        if($component != null) {
            $property  = $this->getProperty();
            if(property_exists($component, $property)) {
                $return_value = $component->$property;
            }
            else if(method_exists($component, 'get' . ucfirst($property))) {
                $return_value = call_user_func(array($component, 'get' . ucfirst($property)));
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('Unknow ' . get_class($component) . ' property: ' . $property);
            }
        }
        return $return_value;
    }

    /**
     * Synchronize server end-point value according to the client end-point
     *
     * @todo add mapping values capability (translation rules for values from server to client end point)
     * 
     * @param __IClientEndPoint $client_end_point
     */
    public function synchronize(__IClientEndPoint &$client_end_point) {
        $value = $client_end_point->getValue();
        return $this->_updateComponentProperty($value);     
    }
    
    protected function _updateComponentProperty($value) {
        $return_value = false;
        $component = $this->getComponent();
        if($component != null) {
            $property  = $this->getProperty();
            if($component->$property !== $value) {
                $component->$property = $value;
                $return_value = true;
            }
        }
        return $return_value;
    }
    
}