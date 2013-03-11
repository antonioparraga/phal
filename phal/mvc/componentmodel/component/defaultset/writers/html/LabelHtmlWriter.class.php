<?php


class __LabelHtmlWriter extends __ComponentWriter {

    public function bindComponentToClient(__IComponent &$component) {
        __UIBindingManager::getInstance()->bind(new __ComponentProperty($component, 'text'), new __HtmlElementCallback($component->getId(), 'update'));
	}
    
    public function startRender(__IComponent &$component) {
        $properties = array();        
        $properties[] = 'id = "' . $component->getId() . '"';
        $properties[] = 'name = "' . $component->getName() . '"';
        if($component->getVisible() == false) {
            $properties[] = 'style = "display : none;"';
        }
        return '<span ' . join(' ', $properties) . '>';
    }
    
    public function endRender(__IComponent &$component)
    {
        return '</span>';
    }    
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        return $component->getText();
    }    
    
    
}
