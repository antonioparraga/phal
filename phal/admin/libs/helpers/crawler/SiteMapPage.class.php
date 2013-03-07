<?php

class __SiteMapPage {

    protected $_uri = null;
    protected $_links = array();
    protected $_backlinks = array();
    protected $_content = null;
    protected $_http_code = null;
    protected $_rank = 0;
    protected $_level = null;
    
    public function __construct($uri) {
        return $this->_uri = $uri;
    }
    
    public function getUri() {
        return $this->_uri;
    }
    
    public function setLinks($links) {
        unset($this->_links);
        $this->_links = array();
        foreach($links as &$link) {
            $this->addLink($link);
        }
    }
    
    public function addLink(__SiteMapLink &$link) {
        $link->setPage($this);
        $this->_links[] =& $link;
    }
    
    public function getLinks() {
        return $this->_links;
    }
    
    public function setHttpCode($http_code) {
        $this->_http_code = $http_code;
    }
    
    public function setContent($content) {
        //$this->_content = $content;
    }
    
    public function getBacklinks() {
        $connection_matrix = __SiteMap::getInstance()->getConnectionMatrix();
        return $connection_matrix[$this->_uri];
    }
    
    public function getContent() {
        return $this->_content;
    }
    
    public function getRoute() {
        $url_parts = parse_url($this->_uri);
        $return_value = __UriFactory::getInstance()->createUri($url_parts['path'])->getRouteId();
        return $return_value;
    }
    
    public function setRank($rank) {
        $this->_rank = $rank;
    }
    
    public function getRank() {
        return $this->_rank;
    }
    
    public function setLevel($level) {
        $this->_level = $level;
    }
    
    public function getLevel() {
        return $this->_level;
    }
    
}
