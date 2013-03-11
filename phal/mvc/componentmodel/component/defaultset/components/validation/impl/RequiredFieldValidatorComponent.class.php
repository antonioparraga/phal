<?php

class __RequiredFieldValidatorComponent extends __ValidatorComponent {

    public function getErrorMessage() {
        if($this->_error_message != null) {
            return $this->_error_message;
        }
        else {
            return $this->_control_to_validate . ' is required';
        }
    }
    
    public function validate() {
        $component_to_validate = $this->getComponentToValidate();
        if($component_to_validate != null) {
            $value = trim($component_to_validate->getValue());
            if(strlen($value) == 0) {
                throw new __ValidationException($this->getErrorMessage());
            }            
        }
    }
        
}
