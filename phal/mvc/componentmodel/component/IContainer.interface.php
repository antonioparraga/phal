<?php

interface __IContainer extends __IComponent {
    
    public function addComponent(__IComponent &$component);
    
    public function &getChildComponents();
    
    public function &getComponentsByClass($class_name);
    
}