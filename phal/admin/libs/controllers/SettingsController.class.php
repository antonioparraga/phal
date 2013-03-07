<?php

class __SettingsController extends __ActionController {

    public function defaultAction() {
        
        $mav = new __ModelAndView('settings');
        try {
            $mav->phal_version = PHAL_VERSION_NUMBER;
            $mav->phal_build_date = PHAL_VERSION_BUILD_DATE;
            $mav->phal_build_changelist = PHAL_VERSION_CHANGE_LIST;
            
            $configuration = __ApplicationContext::getInstance()->getConfiguration();
            $settings = $configuration->getSettings();
            $setting_values = array();
            foreach($settings as $key => $setting) {
                $value = $configuration->getPropertyContent($key);
                if(is_bool($value)) {
                    if($value) {
                        $value = 'true';
                    }
                    else {
                        $value = 'false';
                    }
                }
                $setting_values[] = array('name' => $key, 'value' => $value);
            }
            $mav->settings = $setting_values;
            
            $phal_directives = __Phal::getInstance()->getRuntimeDirectives()->getDirectives();
            $runtime_directives_values = array();
            foreach($phal_directives as $key => $value) {
                if(is_bool($value)) {
                    if($value) {
                        $value = 'true';
                    }
                    else {
                        $value = 'false';
                    }
                }                
                $runtime_directives_values[] = array('name' => $key, 'value' => $value);
            }
            $mav->runtime_directives = $runtime_directives_values;
        }
        catch (Exception $e) {
            $mav->status = 'ERROR';
        }
        return $mav;
    }

    
    
}
        
 