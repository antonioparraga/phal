<?php


class __WindowComponent extends __UIContainer implements __IPoolable {

    protected $_class_name = 'dialog';
    protected $_title = null;
    protected $_url = null;
    protected $_visible = false;
    protected $_content = null;
    protected $_width = 200;
    protected $_height = 150;
    protected $_centered = false;
    protected $_modal = false;
    protected $_show_close_button    = true;
    protected $_show_maximize_button = true;
    protected $_show_minimize_button = true;
    protected $_actionbox = null;

    public function __construct() {
        $this->_actionbox = new __ActionBoxComponent();
    }
    
    public function setAction($action) {
        $this->_actionbox->setAction($action);
    }
    
    public function setController($controller) {
        $this->_actionbox->setController($controller);
    }
    
    public function setParameters($parameters) {
        $this->_actionbox->setParameters($parameters);
    }
    
    public function setClassName($class_name) {
        $this->_class_name = $class_name;
    }

    public function getClassName() {
        return $this->_class_name;
    }

    public function setTitle($title) {
        $this->_title = $title;
    }

    public function getTitle() {
        $return_value = $this->_title;
        return $return_value;
    }

    public function setContent($content) {
        $this->_actionbox->setContent($content);
    }

    public function getContent() {
        $return_value = $this->_actionbox->getContent();
        return $return_value;
    }

    public function setUrl($url) {
        $this->_url = $url;
    }

    public function getUrl() {
        return $this->_url;
    }

    public function setCentered($centered) {
        $this->_centered = (bool) $centered;
    }

    public function getCentered() {
        return $this->_centered;
    }
    
    public function setWidth($width) {
        $this->_width = (int) $width;
    }
    
    public function getWidth() {
        return $this->_width;
    }
    
    public function setHeight($height) {
        $this->_height = $height;
    }
    
    public function getHeight() {
        return $this->_height;
    }
        
    public function setModal($modal) {
        $this->_modal = $this->_toBool($modal);
    }
    
    public function getModal() {
        return $this->_modal;
    }
    
    public function setShowCloseButton($show_close_button) {
        $this->_show_close_button = $this->_toBool($show_close_button);
    }

    public function getShowCloseButton() {
        return $this->_show_close_button;
    }
    
    public function setShowMinimizeButton($show_minimize_button) {
        $this->_show_minimize_button = $this->_toBool($show_minimize_button);
    }
    
    public function getShowMinimizeButton() {
        return $this->_show_minimize_button;
    }
    
    public function setShowMaximizeButton($show_maximize_button) {
        $this->_show_maximize_button = $this->_toBool($show_maximize_button);
    }
    
    public function getShowMaximizeButton() {
        return $this->_show_maximize_button;
    }
    
    public function isEventHandled($event) {
        if(strtoupper($event) == 'CLOSE') {
            return true;    
        }
        else {
            return false;
        }
    }
    
    public function handleEvent(__UIEvent &$event) {
        if(strtoupper($event->getEventName()) == 'CLOSE') {
            $this->_visible = false;
        }
    }
        
}
