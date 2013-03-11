<?php

/**
 * This class represents a configuration section
 *
 */
class __ConfigurationSection extends __ComplexConfigurationComponent {

    protected function addCachedProperty(__ConfigurationProperty &$property) {
        $parent =& $this->getParent();
        $parent->addCachedProperty($property);
    }
    
}