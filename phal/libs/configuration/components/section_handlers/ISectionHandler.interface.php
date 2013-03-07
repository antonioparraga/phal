<?php

/**
 * This is the interface that should implement all classes in charge of resolution of configuration settings.
 * 
 * A {@link __ConfigurationSection} instance can have associated a class implementing the {@link __ISectionHandler}. 
 * When a section with a section handler associated is requested by the {@link __Configuration::getSection} method,
 * the section handler will read the content of the section in order to create other components that will be returned
 * by the getSection instead of the requested {@link __ConfigurationSection} instance.
 * If no section handlers are associated, the original {@link __ConfigurationSection} will be returned.
 * 
 * This mechanism is usefull to transform some configuration pieces in terms of application components just when needed.
 * 
 */
interface __ISectionHandler {
    
    /**
     * Implemented by all configuration section handlers in order to transform a {@link __ConfigurationSection} 
     * instance in tems of application components.
     * 
     * @param __ConfigurationSection $section The {@link __ConfigurationSection} instance to process
     * 
     * @return mixed The configuration section transformed into application components
     */
    public function &process(__ConfigurationSection &$section);
    
}