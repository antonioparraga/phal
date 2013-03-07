<?php

interface __IComponent {

    public function getId();
    
    public function setViewCode($view_code);
    
    public function getViewCode();
    
    public function setContainer(__IContainer &$container);
    
    public function &getContainer();
    
    public function setName($name);

    public function getName();
    
    public function setIndex($index);
    
    public function getIndex();
    
    public function setAlias($alias);
    
    public function getAlias();
    
    public function setDisabled($disabled);
    
    public function getDisabled();

    public function setVisible($enabled);
    
    public function getVisible();
    
    public function hasProperty($property_name);
    
    public function getProperty($property_name);
    
    public function setProperty($property_name, $property_value);

    public function validate();
    
    public function setPersist($persist);
    
    public function getPersist();
    
    public function isEventHandled($event_name);
    
    public function getHandledEvents();
    
    public function handleEvent(__UIEvent &$event);
    
    public function setProgress($progress);
    
    public function getProgress();
    
    public function handleCallback(__IRequest &$request);
    
    public function __toString();
    
}