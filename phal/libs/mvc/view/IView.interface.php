<?php


interface __IView {
    
    public function setCode($view_code);
    
    public function getCode();
    
    public function execute();
    
    public function assign($key, $value = null);
    
    public function isAssigned($key);
    
    public function getAssignedVar($key);
    
}