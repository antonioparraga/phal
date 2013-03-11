<?php

class __CommandLineLocaleNegociator implements __ILocaleNegociator {
    
    public function negociateLocale() {
        $default_lang_iso_code = __ContextManager::getInstance()->getCurrentContext()->getPropertyContent('DEFAULT_LANG_ISO_CODE');
        $return_value = new __Locale($default_lang_iso_code);
        return $return_value;
    }
        
}