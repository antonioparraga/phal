<?php


class __RouteManager {

    private static $_instance = null;
    private $_routes = array();

    private function __construct() {
        $this->startup();
    }
        
    public function startup() {
        $cache = __CurrentContext::getInstance()->getCache();
        $cache_routes_key = '__RouteManager::' . __CurrentContext::getInstance()->getContextId() . '::_routes';
        $routes = $cache->getData($cache_routes_key);
        if($routes != null) {
            $this->_routes = $routes;
        }
        else {
            $routes  = __ApplicationContext::getInstance()->getConfiguration()->getSection('configuration')->getSection('routes');
            $filters = __ApplicationContext::getInstance()->getConfiguration()->getSection('configuration')->getSection('filters');
            if(is_array($routes)) {
                uasort($routes, array($this, 'cmp'));
                $this->_routes = $routes;
                if(is_array($filters)) {
                    foreach($filters as $route_id => $filter_chain) {
                        if($route_id != '*') {
                            if(key_exists($route_id, $this->_routes)) {
                                $route =& $this->_routes[$route_id];
                                $route->setFilterChain($filter_chain);
                                unset($route);
                            }
                            else {
                                throw __ExceptionFactory::getInstance()->createException('ERR_UNKNOW_ROUTE_ID', array($route_id));
                            }
                        }
                        unset($filter_chain);
                    }
                    if(key_exists('*', $filters)) {
                        $global_filters =& $filters['*'];
                        foreach($global_filters as &$global_filter) {
                            foreach($this->_routes as &$route) {
                                if(!$route->hasFilterChain()) {
                                    $filter_chain = new __FilterChain();
                                    $route->setFilterChain($filter_chain);
                                    unset($filter_chain);
                                }
                                $route->getFilterChain()->addFilter($global_filter);
                                unset($route);
                            }
                            unset($global_filter);
                        }
                        unset($global_filters);
                    }
                }
                $cache->setData($cache_routes_key, $this->_routes);
            }
        }
    }
    
    public function cmp($a, $b)
    {
        $return_value = 0; //by default
        if($a->getOrder() === $b->getOrder()) {
            $return_value = 0;
        }
        else if($a->getOrder() === null) {
            $return_value = -1;
        }
        else if($b->getOrder() === null) {
            $return_value = 1;
        }
        else if($a->getOrder() < $b->getOrder()) {
            $return_value = -1;
        }
        else {
            $return_value = 1;
        }
        return $return_value;
    }        
    
    public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __RouteManager();
        }
        return self::$_instance;
    }
    
    public function addRoute(__Route &$route) {
        $this->_routes[$route->getId()] =& $route;
    }
    
    public function hasRoute($route_id) {
        return key_exists($route_id, $this->_routes);
    }
    
    public function &getRoute($route_id) {
        $return_value = null;
        if(key_exists($route_id, $this->_routes)) {
            $return_value =& $this->_routes[$route_id];
        }
        return $return_value;
    }
    
    public function &getRoutes() {
        return $this->_routes;
    }

    public function &getValidRouteForUrl($url) {
        $return_value = null;
        foreach($this->_routes as &$route) {
            if($route->isValidForUrl($url)) {
                $if_parameter = $route->getIfParameter();
                if(!empty($if_parameter) && key_exists($if_parameter, $_REQUEST)) {
                    return $route;
                }
                else if($return_value == null) {
                    $return_value = $route;
                }
            }
        }
        return $return_value;
    }   
    
}

