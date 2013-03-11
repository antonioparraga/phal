<?php

class __RegularExpressionValidatorComponent extends __ValidatorComponent {

    protected $_validation_expression = null;
    protected $_error_message = 'Invalid value';
    
    public function setValidationExpression($validation_expression) {
        $this->_validation_expression = $validation_expression;
    }
    
    public function getValidationExpression() {
        return $this->_validation_expression;
    }
        
    public function validate() {
        $component_to_validate = $this->getComponentToValidate();
        if($component_to_validate != null && 
           $this->_validation_expression != null && 
           !preg_match("/" . $this->_validation_expression . "/i", $component_to_validate->getValue())) {
            throw new __ValidationException($this->getErrorMessage());
        }
    }
        
}
