<?php

/**
 * Collection of arguments used to call a context instance constructor
 * 
 *
 */
class __ConstructorArgumentsCollection extends __Collection {
    
    public function &toArray() {
        $return_value = parent::toArray();
        if(count($return_value) > 0) {
            ksort($return_value);
        }
        return $return_value;
    }
    
}