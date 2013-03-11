<?php

/**
 * This is the base class for configuration storages representing the media where configuration files are stored in
 *
 */
abstract class __ConfigurationStorage {
    
    public function load($filename, __Configuration &$configuration) {
        if(is_readable($filename) && is_file($filename)) {
            try {
                $file_content = file_get_contents($filename);
                $this->parse($file_content, $configuration);
            }
            catch (Exception $e) {
                throw new __ConfigurationException("Error parsing configuration file '$filename':\n\n" . $e->getMessage());
            }
        }
    }    
    
    abstract public function parse($content, __Configuration &$configuration);
    
    abstract public function save($filename, __Configuration &$configuration);

    abstract public function toString(__ConfigurationComponent &$configuration_component);
    
    protected function _parseValue($value, __ComplexConfigurationComponent &$configuration_component) {
        return __ConfigurationValueResolver::resolveValue($value);
    }
    
}