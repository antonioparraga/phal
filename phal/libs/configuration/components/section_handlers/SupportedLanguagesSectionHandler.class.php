<?php

/**
 * This is the section handler in charge of processing &lt;supported-languages&gt; configuration sections
 *
 */
class __SupportedLanguagesSectionHandler extends __CacheSectionHandler {
    
    public function &doProcess(__ConfigurationSection &$section) {
        $languages = array( __ContextManager::getInstance()->getCurrentContext()->getPropertyContent('DEFAULT_LANG_ISO_CODE') => true );
        $subsections = $section->getSections();
        foreach($subsections as &$subsection) {
            if(strtoupper($subsection->getName()) == 'LANGUAGE') {
                $languages[$subsection->getProperty('#text')->getContent()] = true;
            }
        }
        $return_value = array_keys($languages);
        return $return_value;
    }
    
}