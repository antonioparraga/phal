<?php

/**
 * This class represents a configuration property
 *
 */
class __ConfigurationProperty extends __SimpleConfigurationComponent {
    
    public function toArray($useAttr = true)
    {
        $array[$this->_name] = array();
        if ($useAttr && count($this->_attributes) > 0) {
            $array[$this->_name]['#'] = $this->_content;
            $array[$this->_name]['@'] = $this->_attributes;
        } else {
            $array[$this->_name] = $this->_content;
        }
        return $array;
    } 
        
}