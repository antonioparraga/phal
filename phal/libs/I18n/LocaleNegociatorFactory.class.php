<?php

class __LocaleNegociatorFactory {
    
    static public function createLocaleNegociator() {
        $request_type = __Client::getInstance()->getRequestType();
        switch($request_type) {
            case REQUEST_TYPE_HTTP:
            case REQUEST_TYPE_XMLHTTP:
                $return_value = new __HttpLocaleNegociator();
                break;
            case REQUEST_TYPE_COMMAND_LINE:
                $return_value = new __CommandLineLocaleNegociator();
                break;
        }
        return $return_value;
    }
    
}