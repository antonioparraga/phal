<?php

class __SiteMap {

    static protected $_instance = null;
    protected $_pages = array();
    protected $_routes = array();
    protected $_external_links = array();
    protected $_last_update = null;
    
    protected $_connection_matrix = array();
    
    private function __construct() {
        $pages = __ApplicationContext::getInstance()->getCache()->getData('__SiteMap::pages__');
        if(is_array($pages)) {
            $this->_pages = $pages;
            $this->_routes = __ApplicationContext::getInstance()->getCache()->getData('__SiteMap::routes__');
            $this->_connection_matrix  = __ApplicationContext::getInstance()->getCache()->getData('__SiteMap::connection_matrix__');
            $this->_external_links = __ApplicationContext::getInstance()->getCache()->getData('__SiteMap::external_links__');
            $this->_last_update = __ApplicationContext::getInstance()->getCache()->getData('__SiteMap::last_update__');
        }
    }
    
    static public function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __SiteMap();
        }
        return self::$_instance;
    }
    
    public function update($ping_callback = null) {
        $this->clear();
        $crawler = new __Crawler();
        $crawler->crawl($ping_callback);
        $this->_last_update = time();
        $this->saveResults();
    }
    
    public function saveResults() {
        __ApplicationContext::getInstance()->getCache()->setData('__SiteMap::pages__', $this->_pages);
        __ApplicationContext::getInstance()->getCache()->setData('__SiteMap::routes__', $this->_routes);
        __ApplicationContext::getInstance()->getCache()->setData('__SiteMap::connection_matrix__', $this->_connection_matrix);
        __ApplicationContext::getInstance()->getCache()->setData('__SiteMap::last_update__', $this->_last_update);
        __ApplicationContext::getInstance()->getCache()->setData('__SiteMap::external_links__', $this->_external_links);
        
    }
    
    public function clear() {
        unset($this->_pages);
        unset($this->_routes);
        unset($this->_connection_matrix);
        unset($this->_external_links);
        $this->_pages = array();
        $this->_routes = array();
        $this->_connection_matrix = array();
        $this->_external_links = array();
    }

    public function getLastUpdate() {
        return $this->_last_update;
    }
    
    public function hasPage($uri) {
        $return_value = false;
        if(key_exists($uri, $this->_pages)) {
            $return_value = true;
        }
        return $return_value;
    }
    
    public function &getPage($uri) {
        $return_value = null;
        if(key_exists($uri, $this->_pages)) {
            $return_value =& $this->_pages[$uri];
        }
        return $return_value;
        
    }
    
    public function addPage(__SiteMapPage &$page) {
        $uri = $page->getUri();
        $this->_pages[$uri] =& $page;
        $route = $page->getRoute();
        if(!key_exists($route, $this->_routes)) {
            $this->_routes[$route] = array();
        }
        $this->_routes[$route][$uri] =& $page;
        $links = $page->getLinks();
        foreach($links as $link) {
            $href = $link->getHref();
            $url_parts = parse_url($href);
            if($url_parts['host'] != $_SERVER['HTTP_HOST']) {
                $this->_external_links[$href] =& $link;
            }
            if($href != $uri) {
                if(!key_exists($href, $this->_connection_matrix)) {
                    $this->_connection_matrix[$href] = array();
                }
                $this->_connection_matrix[$href][$uri] =& $link;
            }
        }
    }
    
    public function getSiteRank() {
        $return_value = 0;
        $total_pages = count($this->_pages);
        if($total_pages > 0) {
            foreach($this->_pages as $page) {
                $return_value += $page->getRank();
            }
        }
        return $return_value;
    }
    
    public function &getExternalLinks() {
        return $this->_external_links;
    }
    
    public function &getConnectionMatrix() {
        return $this->_connection_matrix;
    }
    
    public function &getPages() {
        return $this->_pages;
    }
    
    public function &getRoutes() {
        return $this->_routes;
    }
    
}
