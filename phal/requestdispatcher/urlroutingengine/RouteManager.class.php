<?php


class __RouteManager {

    private static $_instance = null;
    private $_routes = array();
    private $_all_routes_single_regexp = '.+';
    private $_route_encrypted_to_ids = array();

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

        //compose a single regular expression from all routes (to accelerate the detection of each route)
        if($routes != null) {
            $this->_composeAllRoutesUrlsInOneSingleRegExp($routes);
        }

    }

    private function _composeAllRoutesUrlsInOneSingleRegExp($routes) {
        $cache = __CurrentContext::getInstance()->getCache();
        $cache_all_routes_single_regexp_key = '__RouteManager::' . __CurrentContext::getInstance()->getContextId() . '::_all_routes_single_regexp';
        $cache_route_encrypted_to_ids_key = '__RouteManager::' . __CurrentContext::getInstance()->getContextId() . '::_route_encrypted_to_ids';
        $all_routes_single_regexp = $cache->getData($cache_all_routes_single_regexp_key);
        if($all_routes_single_regexp != null) {
            $this->_all_routes_single_regexp = $all_routes_single_regexp;
            $this->_route_encrypted_to_ids = $cache->getData($cache_route_encrypted_to_ids_key);
        }
        else {
            $routes_regexp_array = array();
            $routes_ids = array();
            foreach($routes as &$route) {
                //global reg-exp is without parameters:
                if($route->getIfParameter() == null) {
                    if (isset($routes_ids[$route->getId()])) {
                        throw __ExceptionFactory::getInstance()->createException('ERR_DUPLICATE_ROUTE_ID', array($route->getId()));
                    }
                    $route_regexp = $route->getUrlRegularExpression();
                    //$route_regexp = trim($route_regexp, "^$");
                    $route_id_encrypted = "R" . substr(md5($route->getId()), 0, 31);
                    $routes_regexp_array[] = '(?P<' . $route_id_encrypted . '>' . $route_regexp . ')';
                    $this->_route_encrypted_to_ids[$route_id_encrypted] = $route->getId();
                }
            }
            if(count($routes_regexp_array) > 0) {
                $this->_all_routes_single_regexp = '(' . join("|", $routes_regexp_array) . ')';
                $cache->setData($cache_all_routes_single_regexp_key, $this->_all_routes_single_regexp);
                $cache->setData($cache_route_encrypted_to_ids_key, $this->_route_encrypted_to_ids);
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
        if(preg_match('/' . $this->_all_routes_single_regexp . '/', $url, $matches) == 1) {
            foreach($matches as $route_encrypted_id => $url_matched) {
                if(isset($this->_route_encrypted_to_ids[$route_encrypted_id]) && $matches[$route_encrypted_id] == $url) {
                    $route_id = $this->_route_encrypted_to_ids[$route_encrypted_id];
                    $route = $this->getRoute($route_id);
                    $if_parameter = $route->getIfParameter();
                    if (!empty($if_parameter) && key_exists($if_parameter, $_REQUEST)) {
                        return $route;
                    }
                    else if ($return_value == null) {
                        $return_value = $route;
                    }
                }
            }
        }
        return $return_value;
    }   
    
}

