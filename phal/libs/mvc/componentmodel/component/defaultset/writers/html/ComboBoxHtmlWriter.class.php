<?php


class __ComboBoxHtmlWriter extends __ComponentWriter {

    public function bindComponentToClient(__IComponent &$component) {
        __UIBindingManager::getInstance()->bind(new __ComponentProperty($component, 'itemValues'), new __HtmlElementProperty($component->getId(), 'items'));
        __UIBindingManager::getInstance()->bind(new __ComponentProperty($component, 'selectedIndex'), new __HtmlElementProperty($component->getId(), 'selectedIndex'));
	}
    
    public function startRender(__IComponent &$component) {
        $properties = array();
        $component_properties = $component->getProperties();
        $style = array();
        foreach($component_properties as $property => $value) {
            if($property != 'STYLE') {
                $properties[] = $property . '="' . $value . '"';
            }
            else {
                $style[] = $value;
            }
        }
        $properties[] = 'id="' . $component->getId() . '"';
        $properties[] = 'name="' . $component->getName() . '"';
        if($component->getVisible() == false) {
            $style[] = 'display : none;';
        }
        if(count($style > 0)) {
            $style_attribute = 'style = "' . implode('', $style) . '"';
        }
        else {
            $style_attribute = null;
        }
        $return_value = '<select ' . implode(' ', $properties) . ' ' . $style_attribute . '>';
        return $return_value;
    }
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        $return_value = '';
        $component_items = $component->getItems();
        foreach($component_items as $component_item) {
            $return_value .= '<option value="' . htmlentities($component_item->getValue()) . '"';
            if($component_item->getSelected()) {
                $return_value .= ' selected="selected"';
            }
            $properties = array();
            $component_item_properties = $component_item->getProperties();
            foreach($component_item_properties as $property => $value) {
                $properties[] = $property . '="' . $value . '"';
            }
            
            $return_value .= ' ' . implode(' ', $properties) . '>' . $component_item->getText() . '</option>';
        }
        return $return_value;
    }
    
    public function endRender(__IComponent &$component)
    {
        return '</select>';
    }    
    
}
