<?php

class __CaptchaHtmlWriter extends __ComponentWriter {

    public function startRender(__IComponent &$component) {
        $component_callback_url = APP_URL_PATH . $component->getId() . '.ccp';
        
        $return_value = <<<CODESET
        <img src="$component_callback_url" id="$component_id">
CODESET;

        return $return_value;
    }
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        return null;
    }
    
    public function endRender(__IComponent &$component) {
        return null;
    }
    
}
