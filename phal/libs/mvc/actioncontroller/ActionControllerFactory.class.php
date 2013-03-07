<?php

class __ActionControllerFactory {

    static public function &createActionController($action_controller_definition, $controller_code = null) {
        $return_value = null;
        $controller_code_substring = null;
        if(strpos($action_controller_definition->getCode(), '*') !== false) {
            if($controller_code != null) {
                $controller_code_substring_array = array();
                if(preg_match('/^' . str_replace('*', '(.+?)', $action_controller_definition->getCode()) . '$/i', $controller_code, $controller_code_substring_array)) {
                    $controller_code_substring = $controller_code_substring_array[1];
                }
                else {
                    return null;
                }
            }
        }
        $controller_class_name = $action_controller_definition->getClass();
        if($controller_code_substring != null) {
            $controller_class_name = str_replace('*', $controller_code_substring, $controller_class_name);
            if(!class_exists($controller_class_name)) {
                throw __ExceptionFactory::getInstance()->createException('ERR_CAN_NOT_RESOLVE_CONTROLLER', array($controller_code));
            }
        }
        if(class_exists($controller_class_name)) {
            $return_value = new $controller_class_name();
            if(! $return_value instanceof __IActionController ) {
                throw __ExceptionFactory::getInstance()->createException('ERR_WRONG_CONTROLLER_CLASS', array(get_class($return_value)));
            }
            $return_value->setCode($controller_code ? $controller_code : $action_controller_definition->getCode());
            $return_value->setHistoriable($action_controller_definition->isHistoriable());
            $return_value->setValidRequestMethod($action_controller_definition->getValidRequestMethod());
            $return_value->setRequestable($action_controller_definition->isRequestable());
            $return_value->setRequireSsl($action_controller_definition->requireSsl());
            if($action_controller_definition->getRequiredPermissionId() != null) {
                $required_permission = __PermissionManager::getInstance()->getPermission($action_controller_definition->getRequiredPermissionId());
                $return_value->setRequiredPermission($required_permission);
            }
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_CLASS_NOT_FOUND', array($controller_class_name));
        }
        return $return_value;
    }      
    
}
