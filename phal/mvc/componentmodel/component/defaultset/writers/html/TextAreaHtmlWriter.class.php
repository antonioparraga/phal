<?php

class __TextAreaHtmlWriter extends __ComponentWriter {

    public function bindComponentToClient(__IComponent &$component) {
        __UIBindingManager::getInstance()->bind(new __ComponentProperty($component, 'value'), new __HtmlElementProperty($component->getId(), 'value'));
	}
    
    public function startRender(__IComponent &$component) {
        $properties = array();
        $component_properties = $component->getProperties();
        foreach($component_properties as $property => $value) {
            $properties[] = $property . '="' . $value . '"';
        }
        $properties[] = 'id="' . $component->getId() . '"';
        $properties[] = 'name="' . $component->getName() . '"';
        if($component->getVisible() == false) {
            $properties[] = 'style = "display : none;"';
        }        
        $return_value = '<textarea ' . implode(' ', $properties) . '>';
        return $return_value;
    }
    
    public function endRender(__IComponent &$component) {
        return '</textarea>';
    }
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        return $component->getValue();
    }    
	
}
