<?php

class __CommandLineRequest extends __Request {

    public function readClientRequest() {
        $parameters = $this->_parseArguments($_SERVER['argv']);
        $this->_requested_parameters[REQMETHOD_COMMAND_LINE] = array_change_key_case($parameters);
        $this->_requested_parameters[REQMETHOD_ALL] = array_change_key_case($parameters);
        $this->_request_method = REQMETHOD_COMMAND_LINE;
    }

    public function getFrontControllerClass() {
        return __CurrentContext::getInstance()->getPropertyContent('COMMAND_LINE_FRONT_CONTROLLER_CLASS');
    }

    public function &getFilterChain() {
        $return_value = null;
        return $return_value;
    }

    public function hasFilterChain() {
        return false;
    }

    protected function _parseArguments($argv) {
        $return_value = array();
        foreach ($argv as $arg) {
            if (preg_match('/\-\-([^=]+)\=(.*)/',$arg, $reg)) {
                $return_value[$reg[1]] = $reg[2];
            } elseif(preg_match('/\-([a-zA-Z0-9])/',$arg, $reg)) {
                $return_value[$reg[1]] = 'true';
            }

        }
        return $return_value;
    }

}