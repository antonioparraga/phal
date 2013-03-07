<?php

class __DateBoxHtmlWriter extends __ComponentWriter {
    
    public function bindComponentToClient(__IComponent &$component) {
        __UIBindingManager::getInstance()->bind(new __ComponentProperty($component, 'value'), new __HtmlElementProperty($component->getId(), 'value'));
	}
    
    public function startRender(__IComponent &$component) {
        $properties = array();
        $component_id = $component->getId();
        $date_format  = $component->getDateFormat();
        $datebox_button_id = $component_id . '_calbutton';
        
        if(__ResponseWriterManager::getInstance()->hasResponseWriter('datebox')) {
            $jod_response_writer = __ResponseWriterManager::getInstance()->getResponseWriter('datebox');
            $jod_setup_response_writer = $jod_response_writer->getResponseWriter('datebox-setup');
        }
        else {
            $jod_response_writer = new __JavascriptOnDemandResponseWriter('datebox');
            $jod_response_writer->addCssFileRef('jscalendar/calendar-green.css');
            $jod_response_writer->addJsFileRef('jscalendar/calendar.js');
            $jod_response_writer->addLoadCheckingVariable('Calendar');
            
            $jod_language_response_writer = new __JavascriptOnDemandResponseWriter('datebox-language');
            $jod_language_response_writer->addJsFileRef('jscalendar/lang/calendar-en.js');
            $jod_language_response_writer->addLoadCheckingVariable('Calendar._DN');
            $jod_response_writer->addResponseWriter($jod_language_response_writer);
            
            $jod_setup_response_writer = new __JavascriptOnDemandResponseWriter('datebox-setup');
            $jod_setup_response_writer->addJsFileRef('jscalendar/calendar-setup.js');
            $jod_setup_response_writer->addLoadCheckingVariable('Calendar.setup');
            $jod_language_response_writer->addResponseWriter($jod_setup_response_writer);
            $javascript_rw = __ResponseWriterManager::getInstance()->getResponseWriter('javascript');
            $javascript_rw->addResponseWriter($jod_response_writer);
        }

        $js_code = <<<CODESET
Calendar.setup({
	inputField:"$component_id",
	ifFormat:"$date_format",
	button:"$datebox_button_id",
	showsTime:false
});
CODESET;

        $jod_setup_response_writer->addJsCode($js_code);

        $component_properties = $component->getProperties();
        foreach($component_properties as $property => $value) {
            $properties[] = $property . '="' . $value . '"';
        }
        $properties[] = 'type="text"';
        $properties[] = 'id="' . $component->getId() . '"';
        $properties[] = 'name="' . $component->getName() . '"';
        $properties[] = 'value="' . $component->getValue() . '"';
        if($component->getVisible() == false) {
            $properties[] = 'style = "display : none;"';
        }
        
        $local_js_lib = __ApplicationContext::getInstance()->getPropertyContent('JS_LIB_DIR');
        $calendar_image_url = __UrlHelper::resolveUrl('jscalendar/calendar.gif', $local_js_lib);

        $return_value = '<input onchange="this.fire(\'phal:validate\');" ' . implode(' ', $properties) . '>&nbsp;<input type="image" src="' . $calendar_image_url . '"  id="' . $datebox_button_id . '" width="16" height="16" border="0">';
        
        return $return_value;
    }
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        return '';
    }
    
    public function endRender(__IComponent &$component) {
        return '';
    }
    
    
}
