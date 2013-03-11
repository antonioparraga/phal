<?php

class __ListBoxHtmlWriter extends __ComboBoxHtmlWriter  {
    
    public function startRender(__IComponent &$component) {
        $properties = array();
        $component_properties = $component->getProperties();
        foreach($component_properties as $property => $value) {
            $properties[] = $property . '="' . $value . '"';
        }
        $properties[] = 'id="' . $component->getId() . '"';
        $properties[] = 'name="' . $component->getName() . '"';
        $properties[] = 'size="' . $component->getRows() . '"';
        if($component->getSelectionMode() == __ItemListComponent::SELECTION_MODE_MULTIPLE ) {
            $properties[] = 'multiple="yes"';
        }
        if($component->getVisible() == false) {
            $properties[] = 'style = "display : none;"';
        }
        $return_value = '<select ' . implode(' ', $properties) . '>';
        return $return_value;
    }
    
    
}
