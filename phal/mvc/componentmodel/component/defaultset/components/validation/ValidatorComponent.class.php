<?php

abstract class __ValidatorComponent extends __UIComponent implements __IPoolable, __IValidator {

    protected $_realtime_validation = true;
    protected $_error_message = null;
    protected $_validation_result = null;
        
    /**
     * Name of input control to validate to
     *
     * @var string
     */
    protected $_control_to_validate = null;

    public function setControlToValidate($control_to_validate) {
        $this->_control_to_validate = $control_to_validate;
    }
    
    /**
     * Retrieves the component name that current validation rule is regarding to
     *
     * @return string
     */
    public function getControlToValidate() {
        return $this->_control_to_validate;
    }
    
    /**
     * Get the component to be validated by the current validator (if applicable)
     *
     * @return __IValueHolder
     */
    public function getComponentToValidate() {
        $return_value = null;
        $component_handler = __ComponentHandlerManager::getInstance()->getComponentHandler($this->_view_code);
        if($component_handler != null) {
            if($component_handler->hasComponent($this->_control_to_validate)) {
                $return_value = $component_handler->getComponent($this->_control_to_validate);
                if(!$return_value instanceof __IValueHolder) {
                    throw __ExceptionFactory::getInstance()->createException('Validations are allowed for components implementing __IValueHolder');
                }                
            }
        }
        return $return_value;
    }
    
  
    /**
     * Set if the associated component must be validated as soon as it lose the focus
     *
     * @param bool $validate_only_on_blur
     */
    public function setRealtimeValidation($realtime_validation) {
        $this->_realtime_validation = $this->_toBool($realtime_validation);
    }
    
    /**
     * Get if the associated component must be validated as soon as it lose the focus
     *
     * @return bool
     */
    public function getRealtimeValidation() {
        return $this->_realtime_validation;
    }

    public function setErrorMessage($error_message) {
        $this->_error_message = $error_message;
    }
    
    public function getErrorMessage() {
        return $this->_error_message;
    }
    

}
