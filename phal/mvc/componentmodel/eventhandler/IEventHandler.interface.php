<?php

/**
 * An event handler is a class in charge of handling all the events raised 
 * by components in a concrete view. In that sense, each event handler is 
 * associated to a view.
 *
 * The create, beforeRender and afterRender methods are called AFTER all the components has been
 * created. The create method is called once the event handler is created, while the beforeRender
 * and afterRender methods are called everytime the view is rendered.
 * The difference between beforeRender and afterRender is that beforeRender is called before rendering
 * the components (by calling to each component writer) while afterRender method is called after all the
 * components has been rendered.
 *  
 */
interface __IEventHandler {
    
    /**
     * Set a viewcode associated to current event handler
     *
     * @param string $view_code
     */
    public function setViewCode($view_code);
    
    /**
     * Gets the viewcode associated to current event handler
     *
     * @return string
     * 
     */
    public function getViewCode();
    
    /**
     * Sets the parent view code, corresponding to a parent viewport (if applicable)
     * i.e., if current view is contained in an actionbox, the parent viewcode is the view containing the actionbox
     *
     * @param string $parent_view_code
     */
    public function setParentViewCode($parent_view_code);
    
    /**
     * Gets the parent view code associated to current event handler (if applicable)
     * 
     * @return string
     *
     */
    public function getParentViewCode();
    
    /**
     * Gets a component identified by a name and (optional) an index in case of component arrays
     *
     * @param string $component_name
     * @param mixed $component_index
     * @return __IComponent
     */
    public function &getComponent($component_name, $component_index = null);
    
    /**
     * Checks if a component is associated to current event handler
     *
     * @param string $component_name
     * @param mixed $component_index
     * @return bool
     */
    public function hasComponent($component_name, $component_index = null);
    
    /**
     * Called once the event handler is created but after components creation
     *
     */
    public function create();
    
    /**
     * Called everytime the view is rendered and before rendering the components
     *
     */
    public function beforeRender();

    /**
     * Called everytime the view is rendered and after rendering the components
     *
     */
    public function afterRender();
    
    /**
     * Called everytime an event is raised and must be handled
     *
     * @param __UIEvent $event
     * @return mixed
     */
    public function handleEvent(__UIEvent &$event);
    
    /**
     * Gets if a component's event is handled by current event handler
     *
     * @param string $event_name
     * @param string $component_name
     * @return bool
     */
    public function isEventHandled($event_name, $component_name);
    
    /**
     * Destroy the current event handler and free the memory
     *
     */
    public function free();
    
    /**
     * Reset the value of all the valueholder components associated to current event handler
     *
     */
    public function resetValueHolders();
    
    
        
}