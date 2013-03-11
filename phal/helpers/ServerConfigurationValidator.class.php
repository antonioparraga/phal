<?php

class __ServerConfigurationValidator {
    
    static public function validate() {
        self::_validatePHP();
        self::_validateRewriteEngine();
    }
    
    static private function _validateRewriteEngine() {
        $rewrite_engine_working = false; //by default
        if(__Client::getInstance()->getRequestType() != REQUEST_TYPE_COMMAND_LINE) {
            $test_url = __UriFactory::getInstance()->createUri()->setRoute('testResponse')->getUrl();
            $test_url = __UrlHelper::resolveUrl($test_url, 'http://' . $_SERVER['SERVER_NAME']);
            if ($stream = @fopen($test_url, 'r')) {
                // print all the page starting at the offset 10
                $test_content = stream_get_contents($stream);
                fclose($stream);
                if($test_content == 'OK') {
                    $rewrite_engine_working = true;
                }
            }
            if($rewrite_engine_working == false) {
                throw __ExceptionFactory::getInstance()->createException('Either mod rewrite is not enabled in your server or is not well configured.');
            }
        }
    }

    static private function _validatePHP() {
        if (version_compare(PHP_VERSION, '5.2.0', '<')) {
            throw __ExceptionFactory::getInstance()->createException('Phal requires PHP 5.2.0 or greater in order to work. Current PHP version is ' . PHP_VERSION . '.');
        }    
        
        $php_extensions = get_loaded_extensions();
        if(in_array('domxml', $php_extensions) || in_array('php_domxml', $php_extensions)) {
            throw __ExceptionFactory::getInstance()->createException('php_domxml extension detected: Need to be disabled.');
        }
    }

}

