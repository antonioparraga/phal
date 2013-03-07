<?php

/**
 * Enter description here...
 *
 */
class __JavascriptObject extends __ClientValueHolder {
    
    public function __construct($instance) {
        $this->setInstance($instance);
    }    
    
    public function getClientValueHolderClass() {
        return '__Object';
    }        
    
}