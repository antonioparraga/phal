<?php

class __ModelValueResolver {
    
    protected $_view;
    
    public function __construct(__View &$view) {
        $this->_view = $view;
    }
    
    public function resolveValue($value) {
        $return_value = $value;
        if($value instanceof __ResourceBase) {
            $return_value = $this->_resolve__ResourceBaseValue($value);
        }
        else if($value instanceof __IDataType) {
            $return_value = $value->getLocalePrintableValue();
        }
        return $return_value;
    }

    protected function _resolve__ResourceBaseValue(__ResourceBase $resource) {
        return $resource->getValue();
    }
        
}