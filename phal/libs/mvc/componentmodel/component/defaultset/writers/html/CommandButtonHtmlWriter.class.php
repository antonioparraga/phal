<?php


class __CommandButtonHtmlWriter extends __ComponentWriter {
    
    public function bindComponentToClient(__IComponent &$component) {
        __UIBindingManager::getInstance()->bindFromServerToClient(new __ComponentProperty($component, 'caption'), new __HtmlElementProperty($component->getId(), 'value'));
        __UIBindingManager::getInstance()->bindFromServerToClient(new __ComponentProperty($component, 'caption'), new __HtmlElementCallback($component->getId(), 'update'));
        __UIBindingManager::getInstance()->bind(new __ComponentProperty($component, 'src'), new __HtmlElementProperty($component->getId(), 'src'));
    }
    
    public function startRender(__IComponent &$component) {
        $properties = array();
        $component_properties = $component->getProperties();
        foreach($component_properties as $property => $value) {
            $properties[] = $property . '="' . $value . '"';
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
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        return '';
    }
    
    public function endRender(__IComponent &$component) {
        return '</input>';
    }
    
    
}
