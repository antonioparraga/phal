<?php


class __CommandButtonHtmlWriter extends __ComponentWriter {
    
    public function bindComponentToClient(__IComponent &$component) {
        __UIBindingManager::getInstance()->bindFromServerToClient(new __ComponentProperty($component, 'caption'), new __HtmlElementProperty($component->getId(), 'value'));
        __UIBindingManager::getInstance()->bindFromServerToClient(new __ComponentProperty($component, 'caption'), new __HtmlElementCallback($component->getId(), 'update'));
        __UIBindingManager::getInstance()->bind(new __ComponentProperty($component, 'src'), new __HtmlElementProperty($component->getId(), 'src'));
    }
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        $enclosed_content = trim($enclosed_content);
        if(!empty($enclosed_content)) {
        	$component->setCaption($enclosed_content);
        }
    }
    
    public function endRender(__IComponent &$component) {
        $properties = array();
        $component_properties = $component->getProperties();
        foreach($component_properties as $property => $value) {
        	$property = strtolower($property);
        	if($property != 'runat') {
        		$properties[] = $property . '="' . $value . '"';
        	}
        }
        if($component->getType() != null) {
            $properties[] = 'type="' . $component->getType() . '"';
        }
        else if($component->getOnClickSubmit()) {
            $properties[] = 'type="submit"';
        }
        else {
            $properties[] = 'type="button"';
        }
        $properties[] = 'id="' . $component->getId() . '"';
        $properties[] = 'name="' . $component->getName() . '"';
        $properties[] = 'value="' . htmlentities($component->getCaption()) . '"';
        
        $image_src = $component->getSrc();
        if($image_src != null) {
            $properties[] = 'src="' . $image_src . '"';
        }
                
        if($component->getVisible() == false) {
            $properties[] = 'style = "display : none;"';
        }
        $return_value = '<input ' . implode(' ', $properties) . '>';
        return $return_value;
    }
    
    
}
