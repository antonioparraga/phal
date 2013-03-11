<?php


/**
 * This is the interface for session handler classes.
 * 
 * If you define a new session handler, you need to implement this interface
 *
 */
interface __ISessionHandler {
    
    /**
     * This method is called when a session is started
     *
     * @param string $save_dir The path to save the session to
     * @param string $session_name The name of the session
     */
    public function open( $save_dir, $session_name );
    
    /**
     * This method is called when a session is closed
     *
     */
    public function close();

    /**
     * This method is called to retrieve information from the session
     *
     * @param string $session_id An identifier of the requested information
     */
    public function read( $session_id );

    /**
     * This method is called to store information to the session
     *
     * @param string $session_id An identifier for the information to store to
     * @param mixed $session_data The data to store to
     */
    public function write( $session_id, $session_data );
    
    /**
     * This method is called to destroy some session information 
     *
     * @param string $session_id An identifier for the information to remove from
     */
    public function destroy( $session_id );
    
    /**
     * This method is called in order to specify the maximum time to invalidate the session
     *
     * @param integer $max_expire_time The maximum time to invalidate the session
     */
    public function gc( $max_expire_time );

    
}