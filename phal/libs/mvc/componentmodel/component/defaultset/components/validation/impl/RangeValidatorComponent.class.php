<?php

class __RangeValidatorComponent extends __ValidatorComponent {

    protected $_minimum_value = null;
    protected $_maximum_value = null;
    
    public function setMinimumValue($minimum_value) {
        if(!is_numeric($minimum_value)) {
            throw __ExceptionFactory::getInstance()->createException('Can not set non-numerical value for minimumValue attribute at RangeValidator component');
        }
        $this->_minimum_value = $minimum_value;
    }
    
    public function getMinimumValue() {
        return $this->_minimum_value;
    }
    
    public function setMaximumValue($maximum_value) {
        if(!is_numeric($maximum_value)) {
            throw __ExceptionFactory::getInstance()->createException('Can not set non-numerical value for minimumValue attribute at RangeValidator component');
        }
        $this->_maximum_value = $maximum_value;
    }
    
    public function getMaximumValue() {
        return $this->_maximum_value;
    }
    
    public function getErrorMessage() {
        if($this->_error_message != null) {
            return $this->_error_message;
        }
        else {
            if($this->_minimum_value !== null && $this->_maximum_value !== null) {
                $type_of_value = 'between ' . $this->_minimum_value . ' and ' . $this->_maximum_value;
            }
            else if($this->_minimum_value !== null) {
                $type_of_value = 'greater than ' . $this->_minimum_value;
            }
            else if($this->_maximum_value !== null) {
                $type_of_value = 'lower than ' . $this->_maximum_value;
            }
            return 'Only values ' . $type_of_value . ' are allowed';
        }
    }
    
    public function validate() {
        $component_to_validate = $this->getComponentToValidate();
        if($component_to_validate != null) {
            $value = $component_to_validate->getValue();
            if(!is_numeric($value)) {
                throw new __ValidationException($this->getErrorMessage());
            }
            if($this->_minimum_value !== null && $value < $this->_minimum_value) {
                throw new __ValidationException($this->getErrorMessage());
            }
            if($this->_maximum_value !== null && $value > $this->_maximum_value) {
                throw new __ValidationException($this->getErrorMessage());
            }
        } 
    }
    
}
