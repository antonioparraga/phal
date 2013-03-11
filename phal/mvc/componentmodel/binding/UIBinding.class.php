<?php

/**
 * This class represents a pair of bound end-points, one representing a server end-point (implementing the {@link __IServerEndPoint}) and the other one representing a client end-point (implementing the {@link __IClientEndPoint}).
 * i.e., a component's property <i>text</i> bound to a HTML element property <i>innerHTML</i>
 *
 * @see __IEndPoint, __UIBindingManager
 * 
 */
final class __UIBinding { 
    
    private $_id = null;
    private $_server_end_point = null;
    private $_client_end_point = null;

    /**
     * Constructor method
     *
     * @param __IComponent &$component A reference to the component
     * @param string $property The property name
     */
    public function __construct(__IServerEndPoint $server_end_point, __IClientEndPoint $client_end_point) {
        $component_id = $server_end_point->getComponent()->getId();
        $client = substr(md5(serialize($client_end_point)), 0, 8);
        $this->_id = preg_replace('/^c/', 'ep-', $component_id) . '-' . $client;
        $this->setServerEndPoint($server_end_point);
        $this->setClientEndPoint($client_end_point);
    }
    
    /**
     * Gets an unique identifier for current instance
     *
     * @return string The id
     */
    public function getId() {
        return $this->_id;
    }
    
    /**
     * Sets a reference to the server end-point
     *
     * @param __IServerEndPoint $server_end_point The server end-point
     */
    public function setServerEndPoint(__IServerEndPoint $server_end_point) {
        $this->_server_end_point = $server_end_point;
        $this->_server_end_point->setUIBinding($this);
    }
    
    /**
     * Gets a reference to the server end-point
     *
     * @return __IServerEndPoint
     */
    public function &getServerEndPoint() {
        return $this->_server_end_point;
    }
    
    /**
     * Sets a reference to the client end-point
     *
     * @param __IClientEndPoint $client_end_point The client end-point
     */
    public function setClientEndPoint(__IClientEndPoint $client_end_point) {
        $this->_client_end_point = $client_end_point;
        $this->_client_end_point->setUIBinding($this);
    }
    
    /**
     * Gets a reference to the client end-point
     *
     * @return __IClientEndPoint
     */
    public function &getClientEndPoint() {
        return $this->_client_end_point;
    }
    
    /**
     * Checks if server and client end-points contains different values
     *
     * @return bool
     */
    public function isDirty() {
        $return_value = false;
        if($this->_server_end_point->getValue() !== $this->_client_end_point->getValue()) {
            $return_value = true;
        }
        return $return_value;
    }
    
    /**
     * Updates the server end-point with the current client end-point value (if applicable)
     *
     */
    public function synchronizeServer() {
        //if both end-points have the BIND_DIRECTION_C2S or equivalent:
        if(($this->_server_end_point->getBoundDirection() & 
           $this->_client_end_point->getBoundDirection() & 
           __IEndPoint::BIND_DIRECTION_C2S) == __IEndPoint::BIND_DIRECTION_C2S ) {
             $this->_server_end_point->synchronize($this->_client_end_point);
        }
    }
    
    /**
     * Updates the client end-point with the current server end-point value (if applicable)
     *
     */
    public function synchronizeClient() {
        //if both end-points have the BIND_DIRECTION_S2C or equivalent:
        if(($this->_server_end_point->getBoundDirection() & 
           $this->_client_end_point->getBoundDirection() & 
           __IEndPoint::BIND_DIRECTION_S2C) == __IEndPoint::BIND_DIRECTION_S2C ) {
            $this->_client_end_point->synchronize($this->_server_end_point);
        }
    }

    
}