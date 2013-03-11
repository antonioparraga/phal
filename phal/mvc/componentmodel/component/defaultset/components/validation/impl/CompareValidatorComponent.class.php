<?php

class __CompareValidatorComponent extends __ValidatorComponent {

    protected $_control_to_compare = null;
    
    public function setControlToCompare($control_to_compare) {
        $this->_control_to_compare = $control_to_compare;
    }
    
    public function getControlToCompare() {
        return $this->_control_to_compare;
    }
    
    public function getErrorMessage() {
        if($this->_error_message != null) {
            return $this->_error_message;
        }
        else {
            return $this->_control_to_validate . ' and ' . $this->_control_to_compare . ' do not match the same value.';
        }
    }
    
    public function getComponentToCompare() {
        $return_value = null;
        $component_handler = __ComponentHandlerManager::getInstance()->getComponentHandler($this->_view_code);
        if($component_handler != null) {
            if($component_handler->hasComponent($this->_control_to_compare)) {
                $return_value = $component_handler->getComponent($this->_control_to_compare);
                if(!$return_value instanceof __IValueHolder) {
                    throw __ExceptionFactory::getInstance()->createException('Validations are allowed for components implementing __IValueHolder');
                }
            }
        }
        return $return_value;
    }    
    
    public function validate() {
        $component_to_validate = $this->getComponentToValidate();
        $component_to_compare  = $this->getComponentToCompare();
        if($component_to_validate != null && $component_to_compare != null) {
            $value = trim($component_to_validate->getValue());
            $value_to_compare = trim($component_to_compare->getValue());
            if($value != $value_to_compare) {
                throw new __ValidationException($this->getErrorMessage());
            }            
        }
    }    
    
}
