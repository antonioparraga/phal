<?php

/**
 * Abstract implementation of server end-point
 *
 */
abstract class __ServerEndPoint implements __IServerEndPoint {
    
    protected $_ui_binding   = null;
    protected $_component_id = null;    
    protected $_view_code    = null;
    protected $_bound_direction = __IEndPoint::BIND_DIRECTION_ALL;
    
    /**
     * Sets the component
     *
     * @param __IComponent $component The component
     */
    public function setComponent(__IComponent &$component) {
        $this->_component_id = $component->getId();
        $this->_view_code    = $component->getViewCode();
    }
    
    /**
     * Gets the component
     *
     * @return __IComponent The component
     */
    public function &getComponent() {
        return __ComponentPool::getInstance()->getComponent($this->_component_id);
    }    
    
    /**
     * Gets the view code associated to the component
     *
     * @return string
     */
    public function getViewCode() {
        return $this->_view_code;
    }    
    
    /**
     * Set the {@link __UIBinding} containing the current server end-point
     *
     * @param __UIBinding $ui_binding The {@link __UIBinding} container
     */
    public function setUIBinding(__UIBinding &$ui_binding) {
        $this->_ui_binding =& $ui_binding;
        $this->getComponent()->addBindingCode($ui_binding->getId());
    }

    /**
     * Gets the {@link __UIBinding} containing the current server end-point
     *
     * @return __UIBinding
     */
    public function &getUIBinding() {
        return $this->_ui_binding;
    }
    
    public function getBoundDirection() {
        return $this->_bound_direction;
    }
    
    public function setBoundDirection($bound_direction) {
        $this->_bound_direction = $bound_direction;
    }
    
}