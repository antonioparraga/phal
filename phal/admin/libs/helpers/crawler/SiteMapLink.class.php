<?php

class __SiteMapLink {

    protected $_page = null;
    protected $_anchor_text = null;
    protected $_href = null;
    protected $_level = null;
    
    public function setAnchorText($anchor_text) { 
        $this->_anchor_text = $anchor_text;
    }
    
    public function getAnchorText() {
        return $this->_anchor_text;
    }
    
    public function setHref($href) {
        $this->_href = $href;
    }
    
    public function getHref() {
        return $this->_href;
    }
    
    public function &getLinkedPage() {
        $return_value = null;
        if(__SiteMap::getInstance()->hasPage($this->_href)) {
            $return_value = __SiteMap::getInstance()->getPage($this->_href);
        }
        return $return_value;
    }
    
    public function setPage(__SiteMapPage &$page) {
        $this->_page =& $page;
    }
    
    public function &getPage() {
        return $this->_page;
    }
    
    public function setLevel($level) {
        $this->_level = $level;
    }
    
    public function getLevel() {
        return $this->_level;
    }
    
}
