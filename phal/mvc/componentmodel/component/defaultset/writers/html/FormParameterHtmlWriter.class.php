<?php


class __FormParameterHtmlWriter extends __ComponentWriter {    
    
    public function startRender(__IComponent &$component)
    {
        if($component->parameterName != null) {
            $input_code = '<input type="hidden" name="' . $this->_parseValue($component->parameterName) . '" value="' . $this->_parseValue($component->parameterValue) . '"/>';
            return $input_code;
        }
    }

    private function _parseValue($value) {
        $return_value = trim($value);
        if(strpos($return_value, 'const:') === 0) {
            $constant_name = trim(substr($return_value, 6)); 
            if(defined($constant_name)) {
                $return_value = constant($constant_name);
            }
            else {
                $return_value = $constant_name;
            }
        }
        if(strpos($return_value, 'prop:') === 0) {
            $property_name = trim(substr($return_value, 5)); 
            $return_value = __ContextManager::getInstance()->getCurrentContext()->getPropertyContent($property_name);
            if($return_value == null) {
                $return_value = $property_name;
            }
        }
        return $return_value;
        
    }  
    
}
