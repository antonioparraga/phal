<?php

class __CustomValidatorComponent extends __ValidatorComponent {

    protected $_client_validation_function = null;
    
    public function setClientValidationFunction($client_validation_function) {
        $this->_client_validation_function = $client_validation_function;
    }
    
    public function getClientValidationFunction() {
        return $this->_client_validation_function;
    }

    
}
