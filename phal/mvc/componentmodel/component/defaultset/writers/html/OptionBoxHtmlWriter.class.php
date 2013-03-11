<?php

class __OptionBoxHtmlWriter extends __ComponentWriter {

    public function bindComponentToClient(__IComponent &$component) {
        $cep = new __HtmlElementProperty($component->getId(), 'value');
        $cep->setValueDomain(__ClientValueHolder::VALUE_DOMAIN_BOOL);
        __UIBindingManager::getInstance()->bind(new __ComponentProperty($component, 'value'), $cep);
        __UIBindingManager::getInstance()->bind(new __ComponentProperty($component, 'caption'), new __HtmlElementCallback($component->getId() . '_caption', 'update'));
	}
    
    public function startRender(__IComponent &$component) {
        $properties = array();
        $component_properties = $component->getProperties();
        foreach($component_properties as $property => $value) {
            $properties[] = $property . '="' . $value . '"';
        }
        $properties[] = 'id="' . $component->getId() . '"';
        $properties[] = 'name="' . $component->getGroup() . '"';     
        if($component->getValue() === true) {
            $properties[] = 'checked = "checked"';
        }
        if($component->getVisible() == false) {
            $properties[] = 'style = "display : none;"';
        }
                
        $return_value  = '<input type="RADIO"  ' . implode(' ', $properties) . '>';
        return $return_value;
    }
    
    public function endRender(__IComponent &$component)
    {
        return '</input>';
    }     
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        $properties = array();
        if($component->getVisible() == false) {
            $properties[] = 'style = "display : none;"';
        }
        return '<span id="' . $component->getId() . '_caption" ' . join(" ", $properties) . '>' . $component->getCaption() . '</span>';
    }    
    
    
}
