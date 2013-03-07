<?php

/**
 * This class represents a session that is not persisted (just in case the browser does is a crawler or just does not accept cookies)
 * 
 * You can get a reference to the __Session instance by calling the {@link __Context::getSession()} method
 * <code>
 * 
 * //Get the session:
 * $session = __ApplicationContext::getInstance()->getSession();
 * 
 * //Retrieve information from the session by reference:
 * $my_data = $session->getData('my_data');
 * 
 * </code>
 *
 */
class __NonPersistentSession implements __IDataContainer {
    
    protected $_session_data = null;
    
    public function __construct($context_id)
    {
        $this->_context_id = $context_id;
        $this->_session_data = array();
    }
    
    /**
     * Returns an identifier for current session
     *
     * @return string
     */
    public function getId() {
        return session_id();
    }
    
    /**
     * Returns if there is any data on session with the given key
     *
     * @param string $key The key to check if there is any data on session
     * @return bool true if there is any data on session with the given key, otherwise false
     */
    public function hasData($key) {
        $key = $this->_parseKey($key);
        return key_exists($key, $this->_session_data);
    }
    
    /**
     * Get data from the session
     *
     * @param string $key The key that identify the information to retrieve from
     * @return mixed The requested session information
     * 
     * @throws __SessionException If the session hasn't been started
     */
    public function &getData($key) {
        $return_value = null;
        $key = $this->_parseKey($key);
        if (key_exists($key, $this->_session_data)) {
            $return_value =& $this->_session_data[$key];
        }
        return $return_value;
    }

    /**
     * Stores data into the session
     * 
     * @param string $key The key that identify the information to store to
     * @param mixed &$data The information to store into the session
     * 
     * @throws __SessionException If the session hasn't been started
     */
    public function setData($key, &$data) {
        $key = $this->_parseKey($key);      
        $this->_session_data[$key] =& $data;
    }

    /**
     * Removes data from the session
     * 
     * @param string $key The key that identify the information to remove from
     * 
     * @throws __SessionException If the session hasn't been started
     */
    public function removeData($key) {
        $key = $this->_parseKey($key);      
        if (key_exists($key, $this->_session_data)) {
            unset($this->_session_data[$key]);
        }
    }    
    
    private function _parseKey($key) {
        $return_value = $key;
        $return_value = str_replace('::', '_', $return_value);
        return $return_value;
    }
    
    /**
     * Alias of clear
     *
     */
    public function destroy() {
        $this->clear();
    }
    
    public function clear() {
        unset($this->_session_data);
        $this->_session_data = array();
    }
        
}
    