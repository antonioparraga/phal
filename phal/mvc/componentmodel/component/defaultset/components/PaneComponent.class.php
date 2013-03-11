<?php

class __PaneComponent extends __UIContainer implements __IPoolable {
    
    protected $_title = null;
    
    public function setTitle($title) {
        $this->_title = $title;
    }
    
    public function getTitle() {
        return $this->_title;
    }
    
}