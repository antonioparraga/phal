<?php


/**
 * This class contain a complete locale information used to handle the i18n and l10n
 *
 */
class __Locale {

    private $_locale_code   = null;
    private $_language_code = null;    
    private $_country_code  = null;
    
    /**
     * Create a new __Locale instance
     */
    function __construct($locale_code) {
        $this->setCode($locale_code);    
    }
    
    public function isEqual($locale) {
        $return_value = false;
        if ($locale->getCode() == $this->getCode()) {
            $return_value = true;
        }
        return $return_value;
    }
    
    public function setCode($locale_code) {
        $return_value = false; //by default
        $locale_code = trim($locale_code);
        if(!empty($locale_code)) { 
            $this->_locale_code = $locale_code;
            $locale_components = preg_split('/[_-]/', $locale_code, 2, PREG_SPLIT_NO_EMPTY);
            $this->_language_code = $locale_components[0];
            if(count($locale_components) > 1) {
                $this->_country_code  = $locale_components[1];
            }
            else {
                $this->_country_code = null;
            }
            $return_value = true; 
        }
        return $return_value;
        
    }

    public function getCode() {
        return $this->_locale_code;
    }
    
    public function getLanguageCode() {
        return $this->_language_code;
    }
    
    public function getCountryCode() {
        return $this->_country_code;
    }

    public function getLanguageIsoCode() {
        return $this->getLanguageCode();
    }
    
    public function getCountryIsoCode() {
        return $this->getCountryCode();
    }
    
}

