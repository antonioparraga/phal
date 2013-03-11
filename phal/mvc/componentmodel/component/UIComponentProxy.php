<?php

class __UIComponentProxy {

    /**
     * Represents the subrogate key used to lookup the receiver instance
     *
     * @var mixed
     */
    protected $_id = null;
    
    /**
     * The index of the component that represents the current proxy
     * @var unknown_type
     */
    protected $_component_index = null;

    /**
     * To be overwrited by child classes by setting the receiver component spec
     *
     * @var string
     */
    protected $_receiver_component_spec = null;
    
    /**
     * Contains a references to the real object the current proxy represents to
     *
     * @var mixed
     */
    protected $_receiver = null;
    
    /**
     * Properties contains properties that can be stored on the virtual proxy until the receiver is loaded.
     * When the receiver is loaded, all the properties will be set, even if them already exists on the receiver.
     *
     * @var array
     */
    protected $_properties = array();
    
    public function isDirty(){
        if($this->_receiver==null){
            return false;
        }else{
            return $this->_receiver->isDirty();
        }
    }
    
    public function setDirty($dirty){
        if($this->_receiver != null) {
            $this->_receiver->setDirty($dirty);
        } 
    }
    
    /**
     * Constructor. It receives the id of the component it represents
     *
     * @param mixed $id
     */
    public function __construct($id) {
        $this->_id = $id;
        $index_matches = array();
        if(preg_match('/[A-Fa-f0-9]+\_(\d+)/', $this->_id, $index_matches)) {
            $this->_component_index = $index_matches[1];
        }
    }    
    
    public function getId() {
        return $this->_id;
    }
    
    public function &__get($property) {
        $receiver = $this->getReceiver();
        if(property_exists($receiver, $property)) {
            return $receiver->$property;
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_PROPERTY_NOT_FOUND', array(get_class($receiver), $property));
        }
    }
    
    public function &__set($property, $value) {
        $receiver = $this->getReceiver();
        if(property_exists($receiver, $property)) {
            $receiver->$property = $value;
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_PROPERTY_NOT_FOUND', array(get_class($receiver), $property));
        }
    }
    
    public function __call($method_name, $parameters) {
        $receiver = $this->getReceiver();
        if($receiver != null) {
            if(method_exists($receiver, $method_name)) {
                $parameter_references = array();
                foreach($parameters as $parameter_name => &$parameter_value) {
                    $parameter_references[$parameter_name] =& $parameter_value;
                }
                $return_value = call_user_func_array(array($receiver, $method_name), $parameter_references);
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('Method not found: ' . $this->getReceiverClass() . '::' . $method_name);
            }
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Error on lazy load: Instance represented by virtual proxy does not exists (proxy class: ' . get_class($this) . ', proxy id: ' . $this->_id . ')');
        }
        return $return_value;
    }

    public function setReceiverClass($receiver_class) {
        $this->_receiver_class = $receiver_class;
    }

    public function getReceiverClass() {
        return $this->_receiver_class;
    }
    
    public function setProperty($property_name, &$property_value) {
        if($this->_receiver == null) {
            $this->_properties[$property_name] =& $property_value;
        }
        else {
            $setter = 'set' . ucfirst($property_name);
            if(method_exists($this->_receiver, $setter)) {
                call_user_func(array($this->_receiver, $setter), $property_value);
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('Method not found on ' . get_class($this->_receiver) . ': ' . $setter);
            }
        }
    }
    
    public function &getProperty($property_name) {
        if(key_exists($property_name, $this->_properties)) {
            $return_value =& $this->_properties[$property_name];
        }
        else {
            $receiver = $this->getReceiver();
            if($receiver != null) {
                $getter = 'get' . ucfirst($property_name);
                if($receiver != null && method_exists($receiver, $getter)) {
                    $return_value = call_user_func(array($receiver, $getter));
                }
                else {
                    throw __ExceptionFactory::getInstance()->createException('Method not found on ' . get_class($receiver) . ': ' . $getter);
                }
            }
        }
        return $return_value;
    }
    
    public function &getReceiver() {
        if($this->_receiver == null) {
            $this->_receiver = __ComponentFactory::getInstance()->createComponent($this->_component_spec, $this->_component_index);
            if($this->_receiver != null) {
                foreach($this->_properties as $property_name => &$property_value) {
                    $setter = 'set' . ucfirst($property_name);
                    if(method_exists($this->_receiver, $setter)) {
                        call_user_func(array($this->_receiver, $setter), $property_value);
                    }
                    else {
                        throw __ExceptionFactory::getInstance()->createException('Method not found on ' . get_class($this->_receiver) . ': ' . $setter);
                    }
                }
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('Error on lazy load: Instance represented by virtual proxy does not exists (proxy class: ' . get_class($this) . ', proxy id: ' . $this->_id . ')');
            }
        }
        return $this->_receiver;
    }
    
    public function isReceiverLoaded() {
        if($this->_receiver != null) {
            $return_value = true;
        }
        else {
            $return_value = false;
        }
        return $return_value;
    }
    
}