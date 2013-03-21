<?php


class __InputBoxHtmlWriter extends __ComponentWriter {
    
    public function bindComponentToClient(__IComponent &$component) {
        __UIBindingManager::getInstance()->bind(new __ComponentProperty($component, 'value'), new __HtmlElementProperty($component->getId(), 'value'));
    }
    
    public function startRender(__IComponent &$component) {
        $component_id = $component->getId();
        $properties = array();
        $component_properties = $component->getProperties();
        foreach($component_properties as $property => $value) {
        	$property = strtolower($property);
        	if($property != 'runat') {
        		$properties[] = $property . '="' . $value . '"';
        	}
        }
        if(!key_exists('TYPE', $component_properties)) {
            $properties[] = 'type="text"';
        }
        $properties[] = 'id="' . $component_id . '"';
        $properties[] = 'name="' . $component->getName() . '"';
        $value = $component->getValue();
        $example_value = $component->getExampleValue();
        if(!empty($value)) {
            $properties[] = 'value="' . htmlentities($value) . '"';
        }
        else if(!empty($example_value)) {
            $properties[] = 'value="' . htmlentities($example_value) . '"';
        }

        if($component->getVisible() == false) {
            $properties[] = 'style = "display : none;"';
        }
        
        $return_value = '<input ' . implode(' ', $properties) . '>';
        
        return $return_value;
    }
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        return '';
    }
    
    public function endRender(__IComponent &$component) {
        return '</input>';
    }
    
    
}
