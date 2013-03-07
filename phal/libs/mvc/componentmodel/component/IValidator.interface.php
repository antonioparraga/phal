<?php

interface __IValidator {
    
    public function validate();
    
    public function resetValidation();
    
    public function getComponentToValidate();
    
}