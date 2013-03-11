<?php

/**
 * Default template locator
 *
 */
class __TemplateLocator extends __FileLocator {
    
    public function getSearchDirs() {
        $return_value = array();
        $template_dir_property = __CurrentContext::getInstance()->getPropertyContent('TEMPLATES_DIR');
        $template_dirs_array = explode(',', $template_dir_property);
        foreach($template_dirs_array as $template_dir) {
            $template_dir = __PathResolver::resolvePath($template_dir);
            if($template_dir != null) {
                $return_value[] = $template_dir;
            }
        }
        return $return_value;
    }
    
}