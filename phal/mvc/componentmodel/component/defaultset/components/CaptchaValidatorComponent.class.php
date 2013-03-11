<?php

class __CaptchaValidatorComponent extends __ValidationRuleComponent {

    protected $_captcha_image_component = null;
    
    public function setCaptcha($captcha_image_component) {
        if(is_string($captcha_image_component)) {
            $this->_captcha_image_component = $captcha_image_component;
        }
    }
    
    /**
     * Get the component to be validated by the current validator
     *
     * @return __IComponent
     */
    public function getCaptcha() {
        $return_value = null;
        $component_handler = __ComponentHandlerManager::getInstance()->getComponentHandler($this->_view_code);
        if($component_handler != null) {
            if($component_handler->hasComponent($this->_captcha_image_component)) {
                $return_value = $component_handler->getComponent($this->_captcha_image_component, $this->_captcha_component_index);
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('Component to validate not found: ' . $this->_component);
            }
        }
        return $return_value;
    }    
    
    protected function _doValidation(__IComponent &$component) {
        $this->_validation_result = true;
        if( $component instanceof __IValueHolder && $component->getEnabled() && $component->getVisible()) {
            $value = $component->getValue();
            $captcha_image_component = $this->getCaptcha();
            if(!$captcha_image_component->check($value)) {
                $this->setErrorMessage( 'The code is invalid' );
                $this->_validation_result = false;
            }            
        }
        if($this->_validation_result == true) {
            $this->_error_message = null;
        }
        return $this->_validation_result;
    }
    
}
