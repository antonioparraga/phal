<?php

/**
 * This is the interfaz that both client and server end points must implement.
 * 
 * @see __IServerEndPoint, __IClientEndPoint
 *
 */
interface __IEndPoint {
    
    /**
     * BIND_DIRECTION_S2C (Server 2 Client) used to allow the synchronization from server to client but not the oposite way
     *
     */
    const BIND_DIRECTION_S2C = 1;
    
    /**
     * BIND_DIRECTION_C2S (Client 2 Server) used to allow the synchronization from client to server but not the oposite way
     *
     */
    const BIND_DIRECTION_C2S = 2;
    
    /**
     * BIND_DIRECTION_ALL used to allow the synchonization from client to server and the oposite way
     *
     */
    const BIND_DIRECTION_ALL = 3;    
    
    /**
     * Sets the UIBinding (a {@link __UIBinding} instance) which current end point belong to
     *
     * @param __UIBinding $ui_binding
     */
    public function setUIBinding(__UIBinding &$ui_binding);

    /**
     * Gets the UIBinding (a {@link __UIBinding} instance) which current end point belong to
     *
     */
    public function &getUIBinding();
    
    /**
     * Set a value representing the bound direction (client to server, server to client or both)
     *
     * @param integer $bound_direction
     */
    public function setBoundDirection($bound_direction);
    
    /**
     * Get a value representing the bound direction (server to client, client to server or both)
     *
     * @return integer
     * 
     */
    public function getBoundDirection();
    
}