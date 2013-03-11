<?php


class __HistoryManager
{
    private $_requests     = null;
    
    static private $_instance = null;

    private function __construct() {
        $session = __ApplicationContext::getInstance()->getSession();
        $this->_requests =& $session->getData('__HistoryManager::_requests');
        if($this->_requests === null) {
            $this->_requests = array();
            $session->setData('__HistoryManager::_requests', $this->_requests);
        }
        else {
            if(key_exists('current_request', $this->_requests)) {
                if(key_exists('last_request', $this->_requests)) {
                    $this->_requests['previous_request'] = $this->_requests['last_request'];
                    unset($this->_requests['last_request']);
                }
                $this->_requests['last_request'] =& $this->_requests['current_request'];
                unset($this->_requests['current_request']);
            }
        }
    }

    /**
     * This method return a singleton instance of __HistoryManager instance
     *
     * @return __HistoryManager
     */
    static public function &getInstance()
    {
        if (self::$_instance == null) {
            // Use "Lazy initialization"
            self::$_instance = new __HistoryManager();
        }
        return self::$_instance;
    }
        
    /**
     * This method creates a new record in the history.
     * The new record will contain at least a copy of current request:
     *
     * @param __Request The requeste to append to the request's history   
     * @return boolean true if the {@link Request} instance was added, else false
     * 
     */
    public function addRequest(__IRequest &$request) {
        $this->_current_request = $request;
        $this->_requests['current_request'] =& $request;
    }
    
    /**
     * Get the last user request
     *
     * @return __IRequest
     */
    public function &getLastRequest() {
        $return_value = null;
        if(key_exists('last_request', $this->_requests)) {
            $return_value =& $this->_requests['last_request'];
        }
        return $return_value;
    }
    
    /**
     * Get the previous user request
     *
     * @return __IRequest
     */
    public function &getPreviousRequest() {
        $return_value = null;
        if(key_exists('previous_request', $this->_requests)) {
            $return_value =& $this->_requests['previous_request'];
        }
        return $return_value;
    }
    
}

