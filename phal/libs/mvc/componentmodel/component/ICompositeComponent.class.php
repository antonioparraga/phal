<?php

interface __ICompositeComponent extends __IContainer {
    
    public function setComponentInterfaceSpec(__UICompositeComponentInterfaceSpec $ui_component_interface);
    
    public function setEventHandler(__ICompositeComponentEventHandler &$event_handler);
    
    public function &getEventHandler();
    
}
