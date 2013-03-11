<?php

class __WebflowExpressionHelper {
    
    static public function resolveExpression($expression) {
        $expression = trim($expression);
        $matched_subexpression = array();
        if(preg_match('/^\{\$([^\}]+)\}$/', $expression, $matched_subexpression)) {
            $subexpression = trim($matched_subexpression[1]);
            $matched_subexpression_components = array();
            if(preg_match('/^([^\.]+)\.(.+)$/', $subexpression, $matched_subexpression_components)) {
                $scope_name = $matched_subexpression_components[1];
                $attribute = $matched_subexpression_components[2];
                $return_value = new __FlowAttribute();
                $scope = self::resolveScope($scope_name);
                $return_value->setScope($scope);
                $return_value->setAttribute($attribute);
            }
        }
        else {
            $return_value = $expression;
        }
        return $return_value;
    }
    
    static public function resolveScope($scope_name) {
        $return_value = null;
        switch(strtoupper($scope_name)) {
            case 'FLOW':
            case 'FLOWSCOPE':
                $return_value = __FlowDefinition::SCOPE_FLOW;
                break;
            case 'REQUEST':
            case 'REQUESTSCOPE':
                $return_value = __FlowDefinition::SCOPE_REQUEST;
                break;
            case 'SESSION':
            case 'SESSIONSCOPE':
                $return_value = __FlowDefinition::SCOPE_SESSION;
                break;
            default:
                throw new __ConfigurationException('Unknown scope ' . $scope_name);
                break;
        }
        return $return_value;
    }
    
   
}