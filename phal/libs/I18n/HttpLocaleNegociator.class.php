<?php

class __HttpLocaleNegociator implements __ILocaleNegociator {

    public function negociateLocale() {
        $front_controller = __FrontController::getInstance();
        $request = $front_controller->getRequest();
        $app_name = md5(__ApplicationContext::getInstance()->getPropertyContent('APP_NAME'));
        if($request != null && $request->hasCookie('__LOCALE__' . $app_name)) {
            $locale_code = $request->getCookie('__LOCALE__' . $app_name);
            $return_value = new __Locale($locale_code);
        }
        else {
            if(key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) {
                $http_accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
            }
            if(!empty($http_accept_language)) {
                //by default:
                if(class_exists('Locale')) {
                    $accepted_locale = Locale::acceptFromHttp($http_accept_language);
                    $candidate_language = Locale::getPrimaryLanguage($accepted_locale);
                }
                else {
                    $accepted_languages = preg_split('/,/', $http_accept_language);
                    $candidate_language   = $this->_getLanguageIsoCode($accepted_languages[0]);
                }
            }
            if(isset($candidate_language) && __I18n::getInstance()->isSupportedLanguage($candidate_language)) {
                $primary_language = $candidate_language;
            }
            else {
                $primary_language = __I18n::getInstance()->getDefaultLanguageIsoCode();
            }
            $return_value = new __Locale($primary_language);
            $auth_cookie = new __Cookie('__LOCALE__' . $app_name, $primary_language, session_cache_expire() * 60 , '/');
            $response = __FrontController::getInstance()->getResponse();
            if($response != null) {
                $response->addCookie($auth_cookie);
            }            
        }
        return $return_value;
    }
    
    private function _getLanguageIsoCode($language) {
        $return_value = trim(preg_replace('/(.*)\;(.*)/', "$1", $language)); //eliminate right part when a ";" is encountered
        //if language have "-", will cut the string in the "-" symbol (f.e. for "en_us" will use "en")
        if(strpos($return_value, "-") !== false) {
            $return_value = preg_replace('/([^-]*)\-(.*)/', "$1", $return_value);
        }
        return $return_value;
    }
     
    
}

