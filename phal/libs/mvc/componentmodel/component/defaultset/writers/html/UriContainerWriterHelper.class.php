<?php

class __UriContainerWriterHelper {
    
    static public function resolveUrl(__IUriContainer &$component) {
        $return_value = $component->getUrl();
        if($return_value == null) {
            $action_identity = new __ActionIdentity($component->getController(), $component->getAction());
            $route_id = $component->getRoute();
            $parameters = array();
            $parameter_list = $component->getParameters();
            if(is_string($parameter_list)) {
                $parameter_list = preg_split('/,/', $parameter_list);
                foreach($parameter_list as $parameter) {
                    $parameter = preg_split('/\s*\=\s*/', $parameter);
                    if(count($parameter) == 2) {
                        $parameters[self::_parseValue($parameter[0])] = self::_parseValue($parameter[1]);
                    }
                }
            }
            else if(is_array($parameter_list)) {
                $parameters = $parameter_list;
            }
            $uri = __UriFactory::getInstance()->createUri()
                                              ->setActionIdentity($action_identity)
                                              ->setParameters($parameters);
            if(!empty($route_id)) {
                $uri->setRouteId($route_id);
            }
            if($component->getUseAbsoluteUrl()) {
                $return_value = $uri->getAbsoluteUrl();
            }
            else {
                $return_value = $uri->getUrl();
            }            
            $component->setUrl($return_value);                                                   
        }
        return $return_value;        
    }
    
    static public function resolveUri(__IUriContainer &$component) {
        $return_value = null;
        if($return_value == null) {
            $action_identity = new __ActionIdentity($component->getController(), $component->getAction());
            $route_id = $component->getRoute();
            $parameters = array();
            $parameter_list = $component->getParameters();
            if(is_string($parameter_list)) {
                $parameter_list = preg_split('/,/', $parameter_list);
                foreach($parameter_list as $parameter) {
                    $parameter = preg_split('/\s*\=\s*/', $parameter);
                    if(count($parameter) == 2) {
                        $parameters[self::_parseValue($parameter[0])] = self::_parseValue($parameter[1]);
                    }
                }
            }
            else if(is_array($parameter_list)) {
                $parameters = $parameter_list;
            }
            $uri = __UriFactory::getInstance()->createUri()
                                              ->setActionIdentity($action_identity)
                                              ->setParameters($parameters);
            if(!empty($route_id)) {
                $uri->setRouteId($route_id);
            }
            $return_value = $uri;
        }
        return $return_value;        
    }    
    
    static private function _parseValue($value) {
        $return_value = trim($value);
        if(strpos($return_value, 'const:') === 0) {
            $constant_name = trim(substr($return_value, 6));
            if(defined($constant_name)) {
                $return_value = constant($constant_name);
            }
            else {
                $return_value = $constant_name;
            }
        }
        if(strpos($return_value, 'prop:') === 0) {
            $return_value = __ApplicationContext::getInstance()->getPropertyContent(trim(substr($return_value, 5)));
        }
        return $return_value;
    }
    
    
}