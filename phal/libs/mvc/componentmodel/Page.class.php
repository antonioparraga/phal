<?php

/**
 * Represents a page rendered into the client browser.
 * 
 * It has accessors to all the shown components within the page, even if they belong to different views.
 *
 */
class __Page {

    static protected $_instance = null;
    
    protected $_id = null;
   
    protected $_component_handlers = array();
    
    protected function __construct() {
        $this->_id = uniqid();
    }
    
    /**
     * Get a singleton instance representing the current page
     *
     * @return __Page
     */
    static public function &getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new __Page();
        }
        return self::$_instance;
    }

    /**
     * Set an identifier for current page.
     *
     * @param unknown_type $id An unique identifier
     */
    public function setId($id) {
        $this->_id = $id;
    }
    
    /**
     * Get the identifier associated to the current page
     *
     * @return string
     */
    public function getId() {
        return $this->_id;
    }
    
    public function hasView($view_code) {
        return key_exists($view_code, $this->_view_codes);
    }
    
    public function addView($view_code) {
        $this->_view_codes[$view_code] = true;
    }
    
    
}