<?php

/**
 * Abstract class implementing the __IComponent interface.
 * 
 * @see __IComponent
 *
 */
abstract class __UIComponent implements __IComponent {
    
    /**
     * Component identifier
     *
     * @var string
     */
    protected $_id           = null;
    
    /**
     * Component name
     *
     * @var string
     */
    protected $_name         = null;
    
    /**
     * An alias for current component. Alias can be used to set a human readable name associated to the component
     *
     * @var string
     */
    protected $_alias        = null;

    /**
     * Component properties
     *
     * @var array
     */
    protected $_properties   = array();
    
    /**
     * The view code of the container view
     *
     * @var string
     */
    protected $_view_code    = null;
    
    /**
     * Component container (if applicable)
     *
     * @var __UIComponent
     */
    protected $_container  = null;
    

    public function setId($id) {
        $this->_id = $id;
    }
    
    /**
     * Read only property that returns an unique identifier for current component
     *
     * @return string
     */
    public function getId() {
        if($this->_id === null) {
            $this->_id = uniqid('c');
        }
        return $this->_id;
    }
    
    /**
     * Sets the code of container view
     *
     * @param string $view_code The view code
     */
    public function setViewCode($view_code) {
        $this->_view_code = $view_code;
    }
    
    /**
     * Gets the code of container view
     *
     * @return string
     */
    public function getViewCode() {
        return $this->_view_code;
    }

    /**
     * Sets a container for current component
     *
     * @param __IContainer $container The component container
     */
    public function setContainer(__IContainer &$container) {
    	//protect to infinite recursion
    	if($this->_container == null || $this->_container->getId() !== $container->getId()) {
    		$this->_container =& $container;
    		$container->addComponent($this);
    	}
    }
    
    /**
     * Gets the first parent container of the given class
     *
     * @return __IComponent the first parent container of specified class, else null if no components are found
     */
    public function &getParentContainerByClass($class_name) {
    	$container =& $this->getContainer();
    	while(!$container instanceof $class_name && $container != null) {
    		$container =& $container->getContainer();
    	}
    	return $container;
    }
    
    public function &getContainer() {
    	return $this->_container;
    }
    
    public function hasContainer() {
    	$return_value = false;
    	if($this->_container != null) {
    		$return_value = true;
    	}
    	return $return_value;
    }
    
    
    
    public function addProperty($property_name, $property_value) {
        $this->_properties[$property_name] = $property_value;
    }
    
    public function getProperties() {
        return $this->_properties;
    }
    
    public function setName($name) {
        $this->_name = $name;
    }

    public function getName() {
        return ($this->_name != null)? $this->_name : $this->_id;
    }
    
    public function setAlias($alias) {
        $this->_alias = $alias;
    }
    
    public function getAlias() {
	    if($this->_alias != null) {
	        $return_value = $this->_alias;
	    }
	    else {
	        $return_value = $this->_name;
	    }
	    return $return_value;
    }
    
    public function __toString() {
        return "(component: $this->_name)";
    }
    
    public function hasProperty($property_name) {
        $return_value = false;
        $property_key = strtoupper($property_name);
        if(property_exists($this, $property_name)) {
            $return_value = true;
        }
        else if(method_exists($this, 'get' . ucfirst($property_name))) {
            $return_value = true;
        }
        else if(key_exists($property_key, $this->_properties)) {
            $return_value = true;
        }
        return $return_value;     
    }
    
    public function getProperty($property_name) {
        $return_value = null;
        $property_key = strtoupper($property_name);
        if(property_exists($this, $property_name)) {
            $return_value = $this->$property_name;
        }
        else if(method_exists($this, 'get' . ucfirst($property_name))) {
            $return_value = call_user_func_array(array($this, 'get' . ucfirst($property_name)), array());
        }
        else if(key_exists($property_key, $this->_properties)) {
            $return_value = $this->_properties[$property_key];
        }
        return $return_value;
    }
    
    public function setProperty($property_name, $property_value) {
        if(property_exists($this, $property_name)) {
            $this->$property_name = $property_value;
        }
        else if(method_exists($this, 'set' . ucfirst($property_name))) {
            call_user_func_array(array($this, 'set' . ucfirst($property_name)), array($property_value));
        }
        else {
            $this->_properties[strtoupper($property_name)] = $property_value;
        }
    }
    
    public function __get($property_name) {
        return $this->getProperty($property_name);
    }

    public function __set($property_name, $property_value) {
        $this->setProperty($property_name, $property_value);
    }
    
    public function __call($method_name, $parameters) {
        $return_value = null;
        $real_method_name = 'do' . ucfirst($method_name);
        //now will redirect to the component method itself:
        if(method_exists($this, $real_method_name)) {
            $return_value = call_user_func_array(array($this->_component, $real_method_name), $parameters);
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_METHOD_NOT_FOUND', array(get_class($this), $method_name));
        }
        return $return_value;
    }    
     
  	protected function _toBool($value) {
        if(is_string($value)) {
            switch(strtoupper($value)) {
                case 'TRUE':
                case 'YES':
                case 'ON':
                    $value = true;
                    break;
                default:
                    $value = false;
                    break;
            }
        }
        else {
            $value = (bool) $value;
        }
        return $value;	    
	}
	
	protected function _toArray($value) {
        $return_value = array();
        if(is_string($value)) {
            $value = preg_split('/,/', $value);
            foreach($value as $parameter) {
                $parameter = preg_split('/\s*\=\s*/', $parameter);
                if(count($parameter) == 2) {
                    $return_value[self::_parseValue($parameter[0])] = self::_parseValue($parameter[1]);
                }
                else {
                    $return_value[] = reset($parameter);
                }
            }
        }
        else if(is_array($value)) {
            $return_value = $value;
        }
        return $return_value;	    
	}
	

    
}