<?php

interface __IComponent {

    public function getId();
    
    public function setViewCode($view_code);
    
    public function getViewCode();
    
    public function setName($name);

    public function getName();
    
    public function setAlias($alias);
    
    public function getAlias();
    
    public function hasProperty($property_name);
    
    public function getProperty($property_name);
    
    public function setProperty($property_name, $property_value);

    public function __toString();
    
}