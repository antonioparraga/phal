<?php

/**
 * This class is called in order to resolve configuration values such placeholder substitutions and so on.
 *
 */
class __ConfigurationValueResolver {

    static protected $_setting_values = array();
    
    public static function addSettingValue($property_name, $property_value) {
        self::$_setting_values[strtoupper($property_name)] = $property_value;
    }
    
    public static function resolveValue($value) {
        $return_value = trim($value);
        if(preg_match('/\$\{([^\}]+)\}/', $value, $values_matched)) {
            $property_name = $values_matched[1];
            if(__CurrentContext::getInstance()->hasProperty($property_name)) {
                $property_value = __CurrentContext::getInstance()->getPropertyContent($property_name);
            }
            else if(key_exists(strtoupper($property_name), self::$_setting_values)) {
                $property_value = self::$_setting_values[$property_name];
            }
            else {
                throw new __ConfigurationException('Unknown property ' . $property_name . '. Make sure that it has been parsed before used.');
            }
            $return_value = str_replace('${' . $property_name . '}', $property_value, $return_value);
        }
        else if(strpos($return_value, 'const:') === 0) {
            $constant_name = trim(substr($return_value, 6));
            if(defined($constant_name)) {
                $return_value = constant($constant_name);
            }
            else {
                throw new __ConfigurationException('Unknown constant ' . $constant_name);
            }
        }
        else if(strpos($return_value, 'prop:') === 0) {
            $property_name  = trim(substr($return_value, 5));
            if(__CurrentContext::getInstance()->hasProperty($property_name)) {
                $return_value = __CurrentContext::getInstance()->getPropertyContent($property_name);
            }
            else {
                throw new __ConfigurationException('Unknown property ' . $property_name . '. Make sure that it has been parsed before used.');
            }
        }
        else if(strtoupper($return_value) == 'TRUE') {
            $return_value = true;
        }
        else if(strtoupper($return_value) == 'FALSE') {
            $return_value = false;
        }
        return $return_value;
    }
    
    public static function toBool($value) {
        if(is_string($value)) {
            switch(strtoupper($value)) {
                case 'TRUE':
                case 'YES':
                    $value = true;
                    break;
                default:
                    $value = false;
                    break;
            }
        }
        else {
            $value = (bool) $value;
        }
        return $value;      
    }
    
}