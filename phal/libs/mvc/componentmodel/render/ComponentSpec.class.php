<?php


/**
 * This class contains the information to create and render a component.
 *
 */
final class __ComponentSpec {
	
    /**
     * The UI tag used to build the current instance
     *
     * @var string
     */
    protected $_tag = null;
    
    /**
     * The component class
     *
     * @var string
     */
    protected $_class = null;
    
    /**
     * The component writer class
     *
     * @var string
     */
    protected $_writer = null;
    
    /**
     * Component default values
     *
     * @var array
     */
    protected $_default_values = array();
    
    /**
     * An unique id
     *
     * @var string
     */
    protected $_id = null;
    
    protected $_is_array = false;
    
    protected $_index = null;
    
    protected $_ui_component_interface = null;
    
    protected $_view_code = null;
    
    protected $_run_at_server = true;
    
    /**
     * Constructor.
     *
     * @param string $tag_name The UI tag
     * @param string $component_class The component class
     */
    public function __construct($tag_name, $component_class) {
        if(!class_exists($component_class)) {
            throw __ExceptionFactory::getInstance()->createException('ERR_CLASS_NOT_FOUND', array($component_class));
        }
        if(!is_subclass_of($component_class, '__IComponent')) {
            throw __ExceptionFactory::getInstance()->createException('ERR_UNEXPECTED_CLASS', array($component_class, '__IComponent'));
        }
        $this->_tag   = $tag_name;
        $this->_class = $component_class;
    }
    
    /**
     * Set the component writer associated to the current instance
     *
     * @param __IComponentWriter &$component_writer The component writer class
     */
    public function setWriter(__IComponentWriter &$component_writer) {
        $this->_writer =& $component_writer;
    }

    public function &getWriter() {
        return $this->_writer;
    }
    
    /**
     * Gets an unique id for current instance
     *
     * @return string
     */
    public function getId() {
        if($this->_id == null) {
            if(key_exists('name', $this->_default_values)) {
                $uniqstr = $this->_view_code . ':' . $this->_default_values['name'];
                $this->_id = substr(md5($uniqstr), 0, 8);
            }
            else {
                $this->_id = uniqid();
            }
        }
        return $this->_id;
    }

    public function setId($id) {
        $this->_id = $id;
    }
    
    public function setViewCode($view_code) {
        $this->_view_code = $view_code;
    }
    
    public function getViewCode() {
        return $this->_view_code;
    }
    
    /**
     * Gets the name property associated to the component, or
     * the component spec id if no names was set.
     *
     * @return string
     */
    public function getName() {
        if(key_exists('name', $this->_default_values)) {
            $return_value = $this->_default_values['name'];
        }
        else {
            $return_value = $this->getId();
        }
        return $return_value;
    }
    
    /**
     * Gets the tag used to build the current instance
     *
     * @return string
     */
    public function getTag() {
        return $this->_tag;
    }
    
    /**
     * Gets the component class associated to current instance
     *
     * @return string
     */
    public function getClass() {
        return $this->_class;
    }
        
    /**
     * Set a default value associated to the component represented by current instance
     *
     * @param string $property_name Property name
     * @param mixed $property_value Property value
     */
    public function addDefaultValue($property_name, $property_value) {
        if(strtoupper($property_name) == 'NAME') {
            if(preg_match('/^([^\[]+)\[\]$/', $property_value, $matched)) {
                $property_value = $matched[1];
                $this->_is_array = true;
            }
        }
        $this->_default_values[$property_name] = $property_value;
    }
    
    /**
     * Set all the default values associated to the component represented by current instance
     *
     * @param array $default_values An array of property pairs [name, value]
     */
    public function setDefaultValues(array $default_values) {
        foreach($default_values as $name => $value) {
            $this->addDefaultValue($name, $value);
        }
    }
    
    /**
     * Get the default value associated to the component represented by current instance
     *
     * @param string $property_name Property name
     * @return mixed The value
     */
    public function getDefaultValue($property_name) {
        $return_value = null;
        $property_name = strtolower($property_name);
        if(key_exists($property_name, $this->_default_values)) {
            $return_value = $this->_default_values[$property_name];
        }
        return $return_value;
    }

    public function getDefaultValues() {
        return $this->_default_values;
    }    
    
    public function __get($key) {
       return $this->getDefaultValue($key);
    }

    public function __set($key, $value) {
        $this->addDefaultValue($key, $value);
    }
    
    public function isArray() {
        return $this->_is_array;
    }
    
    public function setIndex($index) {
        $this->_index = $index;
    }
    
    public function getIndex() {
        return $this->_index;
    }    
    
    public function setComponentInterfaceSpec(__UICompositeComponentInterfaceSpec $ui_component_interface) {
        $this->_ui_component_interface = $ui_component_interface;
    }    
    
    public function getComponentInterfaceSpec() {
        return $this->_ui_component_interface;
    }
    
    public function setRunAtServer($run_at_server) {
        $this->_run_at_server = (bool) $run_at_server;
    }
    
    public function getRunAtServer() {
        return $this->_run_at_server;
    }
    
}
