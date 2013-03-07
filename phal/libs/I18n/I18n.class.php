<?php


/**
 * This class is the class in charge of manage all internationalization (i18n) stuffs.
 * 
 */
class __I18n {

    /**
     * An array of all {@link Locale} instances used in the current session
     *
     * @var array
     */
    private $_locale = null;

    /**
     * An array of iso codes for supported languages in the current application
     * 
     * @var array
     */
    private $_supported_languages = array( );
    
    /**
     * The default language to be used
     * 
     * @var string
     */
    private $_default_language_iso_code = 'en'; //by default, english
    
    /**
     * This is the singleton static variable that contains current shared instance
     *
     * @var __I18n
     */
    static private $_instance = null;

    /**
     * Create a new __I18n instance
     */
    private function __construct()
    {
        $this->_loadSupportedLanguages();
    }
    
    /**
     * This method returns a singleton instance of __I18n
     *
     * @return __I18n A singleton reference to the __I18n
     */
    static public function &getInstance()
    {
        if (self::$_instance == null) {
            // Use "Lazy initialization"
            self::$_instance = new __I18n();
            //once the __I18n has been built, let's negociate the locale with the client:
            self::$_instance->_negociateLocale();
        }
        return self::$_instance;
    }
        
    protected function _negociateLocale() {
        $locale_negociator = __LocaleNegociatorFactory::createLocaleNegociator();
        $locale = $locale_negociator->negociateLocale();
        $this->_locale = $locale;
    }
    
    protected function _loadSupportedLanguages() {
        $context = __CurrentContext::getInstance();
        $this->_default_language_iso_code = $context->getPropertyContent('DEFAULT_LANG_ISO_CODE');
        $supported_languages = $context->getConfiguration()->getSection('configuration')->getSection('supported-languages');
        if(is_array($supported_languages )) {
            $this->_supported_languages = &$supported_languages;
        }
        else {
            $this->_supported_languages = array( $this->_default_language_iso_code );
        }
    }
    
    public function addSupportedLanguage($language_iso_code) {
        if(!in_array($language_iso_code, $this->_supported_languages)) {
            $this->_supported_languages[] = $language_iso_code;
        }
    }
    
    public function isSupportedLanguage($language_iso_code) {
        return in_array($language_iso_code, $this->_supported_languages);
    }
    
    public function getDefaultLanguageIsoCode() {
        return $this->_default_language_iso_code;
    }
    
    public function getSupportedLanguages() {
        return $this->_supported_languages;
    }
    
    /**
     * This method adds a new __Locale to the internal collection of locales, and set it as the default locale
     *
     * @param Locale The locale to set
     * @return boolean true if the locale has been setted successfully, else false
     */
    public function setLocale(__Locale &$locale) {
        $this->_locale =& $locale;
        if(__Client::getInstance()->getRequestType() == REQUEST_TYPE_HTTP) {
            $app_name = md5(__ApplicationContext::getInstance()->getPropertyContent('APP_NAME'));
            $auth_cookie = new __Cookie('__LOCALE__' . $app_name, $locale->getCode(), session_cache_expire() * 60 , '/');
            $response = __FrontController::getInstance()->getResponse();
            $response->addCookie($auth_cookie);
        }
    }
    
    /**
     * This method return the last setted locale (that it's also the default locale)
     *
     * @return Locale The requested locale, or null if no locales are stored
     */
    public function &getLocale() {
        return $this->_locale;
    }
    
}
    
    
    