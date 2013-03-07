<?php

class __FlowState implements __IFlowState {

    protected $_id = null;
    protected $_attributes_collection = null;
    
    public function __construct() {
        $this->_attributes_collection = new __FlowAttributesCollection();
    }
    
    public function setId($id) {
        $this->_id = $id;
    }
    
    public function getId() {
        return $this->_id;
    }
    
    public function addAttribute($attribute) {
        $this->_attributes_collection->add($attribute);
    }
    
    public function getAttributes() {
        return $this->_attributes_collection->toArray();
    }
    
    
}
