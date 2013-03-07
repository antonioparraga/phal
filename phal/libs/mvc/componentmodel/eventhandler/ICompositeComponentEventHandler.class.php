<?php

interface __ICompositeComponentEventHandler extends __IEventHandler {
    
    public function setCompositeComponent(__ICompositeComponent &$composite_component);
    
    public function &getCompositeComponent();
    
    public function setupProperties();
    
    public function raiseEvent($event_name, $extra_info = array());    
    
}
