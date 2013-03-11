<?php

/**
 * This is the factory for configuration storages
 * 
 * @see __ConfigurationStorage
 *
 */
final class __ConfigurationStorageFactory {
        
    static public function createConfigurationStorage($file_type) {
        $return_value = null;
        switch ($file_type) {
            case CONFIGURATION_TYPE_XML:
                $return_value = new __XMLConfigurationStorage();
                break;
            case CONFIGURATION_TYPE_INI:
            default:
                $return_value = new __IniFileConfigurationStorage();
                break;
        }
        return $return_value;
    }
    
}