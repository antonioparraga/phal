<?php

class __HtmlElementAccessor extends __ClientValueHolder {
    
    /**
     * Bound direction will depend on getter and setter definition, since we can define one of them but not the other one.
     * By default, no bound direction
     *
     * @var integer
     */
    protected $_bound_direction = 0;
    protected $_getter = null;
    protected $_setter = null;

    public function __construct($instance, $getter = null, $setter = null) {
        $this->setGetter($getter);
        $this->setSetter($setter);
        $this->setInstance($instance);
    }
    
    public function setGetter($getter) {
        if(!empty($getter)) {
            $this->_getter = $getter;
            $this->_bound_direction = $this->_bound_direction | __IEndPoint::BIND_DIRECTION_C2S;
        }
    }
    
    public function getGetter() {
        return $this->_getter;
    }
    
    public function setSetter($setter) {
        if(!empty($setter)) {
            $this->_setter = $setter;
            $this->_bound_direction = $this->_bound_direction | __IEndPoint::BIND_DIRECTION_S2C;
        }
    }
    
    public function getSetter() {
        return $this->_setter;
    }
    
    public function getClientValueHolderClass() {
        return '__ElementAccessor';
    }    
    
    public function getSetupCommand() {
        $return_value = null;
        if($this->_getter != null || $this->_setter != null) {
            $data = array();
            $data['code']     = $this->getUIBinding()->getId();
            $data['receiver'] = $this->_instance;
            if($this->_getter != null) {
                $data['getter'] = $this->_getter;
            }
            if($this->_setter != null) {
                $data['setter'] = $this->_setter;
            }
            $data['valueHolderClass'] = $this->getClientValueHolderClass();
            $command_data = array('valueHolderData' => $data);
            $return_value = new __AsyncMessageCommand();
            $return_value->setClass('__RegisterValueHolderCommand');
            $return_value->setData($command_data);
            $this->setAsSynchronized();
        }          
        return $return_value;
    }

    public function getCommand() {
        $return_value = null;
        if($this->isUnsynchronized() && ($this->_getter != null || $this->_setter != null)) {
            $data = array();
            $data['code']  = $this->getUIBinding()->getId();
            $data['value'] = $this->_value;
            $return_value  = new __AsyncMessageCommand();
            $return_value->setClass('__UpdateValueHolderCommand');
            $return_value->setData($data);
            $this->setAsSynchronized();
        }
        return $return_value;
    } 
    
}


