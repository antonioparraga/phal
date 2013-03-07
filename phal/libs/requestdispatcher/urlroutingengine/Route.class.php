<?php

class __Route {
    
    private $_id                      = null;
    private $_url_pattern             = null;
    private $_front_controller_class  = null;
    private $_action_identity         = null;
    private $_flow_id                 = null;
    private $_url_regular_expression  = null;
    private $_variable_patterns       = array();
    private $_variables_order         = array();
    private $_fixed_parameters        = array();
    private $_if_equals               = array();
    private $_if_isset                = array();
    private $_variable_values         = array();
    private $_dirty                   = false;
    private $_filter_chain            = null;
    private $_url_builder             = null;
    private $_cache                   = false;
    private $_if_parameter            = null;    
    private $_supercache              = false;
    private $_cache_ttl               = null;
    private $_route_id_to_redirect_to = null;
    private $_only_ssl                = false;
    private $_redirection_code        = 302;
    protected $_order = null;

    public function __Route() {
        $this->_front_controller_class = __CurrentContext::getInstance()->getPropertyContent('HTTP_FRONT_CONTROLLER_CLASS');
        $this->_action_identity        = new __ActionIdentity();
    }
    
    public function setId($id) {
        $this->_id = $id;
    }
    
    public function getId() {
        return $this->_id;
    }
    
    public function setOrder($order) {
        $this->_order = $order;
    }
    
    public function getOrder() {
        return $this->_order;
    }
    
    public function setUrlPattern($url_pattern) {
        if($this->_url_pattern != $url_pattern) {
            $this->_url_pattern = $url_pattern;
            $this->_dirty = true;
        }
    }
     
    public function isValidForUrl($url) {
        $return_value = false;
        $url_regular_expression = $this->getUrlRegularExpression();
        $variables_matched = array();
        if(preg_match('/' . $url_regular_expression . '/', $url, $variables_matched)) {
            $return_value = true;
            if(!empty($this->_if_parameter) && !key_exists($this->_if_parameter, $_REQUEST)) {
                $return_value = false;
            }            
        }
        return $return_value;
    }
    
    public function getUrlPattern() {
        return $this->_url_pattern;
    }
    
    public function setFilterChain(__FilterChain &$filter_chain) {
        $this->_filter_chain =& $filter_chain;
    }
    
    public function &getFilterChain() {
        return $this->_filter_chain;
    }
    
    public function hasFilterChain() {
        if($this->_filter_chain == null) {
            return false;
        }
        else {
            return true;
        }
    }
    
    public function setVariablePatterns(array $variable_patterns) {
        if($this->_variable_patterns != $variable_patterns) {
            $this->_variable_patterns = $variable_patterns;
            $this->_dirty = true;
        }
    }
    
    public function getVariablePatterns() {
        return $this->_variable_patterns;
    }

    public function setFrontControllerClass($front_controller_class) {
        $this->_front_controller_class = $front_controller_class;
    }
    
    public function getFrontControllerClass() {
        return $this->_front_controller_class;
    }

    public function setActionIdentity(__ActionIdentity $action_identity) {
        $this->_action_identity = $action_identity;
    }
    
    public function getActionIdentity() {
        return $this->_action_identity;
    }    

    public function setActionCode($action_code) {
        $this->_action_identity->setActionCode($action_code);
    }
    
    public function setControllerCode($controller_code) {
        $this->_action_identity->setControllerCode($controller_code);
    }

    public function setFlowId($flow_id) {
        $this->_flow_id = $flow_id;
    }
    
    public function getFlowId() {
        return $this->_flow_id;
    }
    
    public function addFixedParameter($parameter_name, $parameter_value) {
        $this->_fixed_parameters[$parameter_name] = $parameter_value;
    }
    
    public function setFixedParameters(array $fixed_parameters) {
        $this->_fixed_parameters = $fixed_parameters;
    }
    
    public function getFixedParameters() {
        return $this->_fixed_parameters;
    }
    
    public function addIfIssetCondition($variable, array $parameters) {
        $this->_if_isset[$variable] = $parameters;
    }
    
    public function getIfIssetConditions() {
        return $this->_if_isset;
    }
    
    public function addIfEqualsCondition($variable, $value, array $parameters) {
        if(! key_exists($variable, $this->_if_equals) ) {
            $this->_if_equals[$variable] = array();
        }
        if(! key_exists($value, $this->_if_equals[$variable]) ) {
            $this->_if_equals[$variable][$value] = array();
        }
        $this->_if_equals[$variable][$value] = $parameters;        
    }
    
    public function getIfEqualsConditions() {
        return $this->_if_equals;
    }
    
    public function setVariableValue($variable_name, $variable_value) {
        $this->_variable_values[$variable_name] = $variable_value;
    }
    
    public function getUrlRegularExpression() {
        $this->resolveRouteComponents();
        return $this->_url_regular_expression;
    }
    
    public function getVariablesOrder() {
        $this->resolveRouteComponents();
        return $this->_variables_order;
    }
    
    public function resolveRouteComponents() {
        if($this->_dirty == true) {
            $this->_variables_order = array();
            $url_regular_expression = preg_replace('/\(([^?)])/', '(?:$1', $this->_url_pattern);
            foreach($this->_variable_patterns as $varpattern_name => $varpattern) {
                if(substr($varpattern, 0, 1) == '^') {
                    $varpattern = substr($varpattern, 1);
                }
                $varpattern_length = strlen($varpattern);
                if(substr($varpattern, $varpattern_length - 1, 1) == '$') {
                    $varpattern = substr($varpattern, 0, $varpattern_length - 1);
                }
                $varpattern = preg_replace('/\(([^?)])/', '(?:$1', $varpattern);
                $url_regular_expression = preg_replace('/' . str_replace('$', '\$', $varpattern_name) . '([^_A-Za-z0-9]|$)/', '(' . $varpattern . ')$1', $url_regular_expression);
                $this->_variables_order[] = $varpattern_name;
            }
            $this->_url_regular_expression = $url_regular_expression;
            $this->_url_builder = __UrlBuilderFactory::createUrlBuilder($this);
            $this->_dirty = false;
        }
    }

    public function getUrlBuilder() {
        $this->resolveRouteComponents();
        return clone $this->_url_builder;
    }

    public function getParameters($variable_values) {
        $this->resolveRouteComponents();
        $request_parameters = $this->_doGetParameters($this->getFixedParameters(), $variable_values);
        foreach($this->_if_isset as $variable_name => $parameters) {
            if(key_exists($variable_name, $variable_values) && !empty($variable_values[$variable_name])) {
                $request_parameters += $this->_doGetParameters($parameters, $variable_values);
            }
        }
        foreach($this->_if_equals as $variable_name => $values) {
            if(key_exists($variable_name, $variable_values) && key_exists($variable_values[$variable_name], $values)) {
                $request_parameters += $this->_doGetParameters($values[$variable_values[$variable_name]], $variable_values);
            }
        }        
        return $request_parameters;
    }    
    
    private function _doGetParameters($parameters, $variable_values) {
        $return_value = array();
        foreach($parameters as $parameter_name => $parameter_value) {
            if(key_exists($parameter_value, $variable_values)) {
                $parameter_value = $variable_values[$parameter_value];
            }
            $return_value[$parameter_name] = $parameter_value;
        }
        return $return_value;
    }    
    
    public function setCache($cache) {
        $this->_cache = (bool) $cache;
    }
    
    public function getCache() {
        return $this->_cache;
    }

    public function setSuperCache($supercache) {
        $this->_supercache = (bool) $supercache;
    }
    
    public function getSuperCache() {
        return $this->_supercache;
    }
    
    public function setCacheTtl($cache_ttl) {
        if(is_numeric($cache_ttl)) {
            $this->_cache_ttl = (int) $cache_ttl;
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Wrong parameter value for cache ttl: ' . $cache_ttl);
        }
    }
    
    public function getCacheTtl() {
        return $this->_cache_ttl;
    }
    
    public function setOnlySSL($only_ssl) {
    	$this->_only_ssl = (bool) $only_ssl;
    }
    
    public function getOnlySSL() {
    	return $this->_only_ssl;
    }
    
    public function setRouteToRedirectTo($route_id, $redirection_code) {
        $this->_route_id_to_redirect_to = $route_id;
        $this->_redirection_code = $redirection_code;
    }
    
    public function getRouteToRedirectTo() {
        $return_value = null;
        $route_id_to_redirect_to = $this->_route_id_to_redirect_to;
        if(!empty($route_id_to_redirect_to)) {
            $route_manager = __RouteManager::getInstance();
            if($route_manager->hasRoute($route_id_to_redirect_to)) {
                 $return_value = $route_manager->getRoute($route_id_to_redirect_to);
            }
        }
        return $return_value;
    }
    
    public function getRouteIdToRedirectTo() {
        return $this->_route_id_to_redirect_to;
    }
    
    public function setRedirectionCode($redirection_code) {
        $this->_redirection_code = $redirection_code;
    }
    
    public function getRedirectionCode() {
        return $this->_redirection_code;
    }
    
    public function setIfParameter($if_parameter) {
        $this->_if_parameter = $if_parameter;
    }
    
    public function getIfParameter() {
        return $this->_if_parameter;
    }    
    
}