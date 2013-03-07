<?php

/**
 * Represents a component's method as a server end-point
 * 
 * @see __IServerEndPoint, __IEndPoint, __UIBinding
 *
 */
class __ComponentMethod extends __ServerEndPoint {
    
    protected $_method = null;
    protected $_bound_direction = __IEndPoint::BIND_DIRECTION_S2C;
    /**
     * Constructor method
     *
     * @param __IComponent $component The component associated to this end-point
     * @param string $method The component method
     */
    public function __construct(__IComponent &$component, $method) {
        $this->setComponent($component);
        $this->setMethod($method);
    }
    
    public function receiveComponentNotification($accessor) {
        if($accessor == $this->_method) {
            $this->_ui_binding->synchronizeClient();
        }
    }
    
    /**
     * Sets the method
     *
     * @param string $method The method
     */
    public function setMethod($method) {
        $this->_method = $method;
    }
    
    /**
     * Gets the method
     *
     * @return string
     */
    public function getMethod() {
        return $this->_method;
    }

    /**
     * Gets the bound direction allowed by this end-point
     *
     * @return integer
     */
    public function getBoundDirection() {
        return __IEndPoint::BIND_DIRECTION_S2C;
    }
    
    public function synchronize(__IClientEndPoint &$client_end_point) {
		//nothing to do
    }    
    
}