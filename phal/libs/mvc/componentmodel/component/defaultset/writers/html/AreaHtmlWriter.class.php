<?php

class __AreaHtmlWriter extends __ComponentWriter {

    public function bindComponentToClient(__IComponent &$component) {
        __UIBindingManager::getInstance()->bindFromServerToClient(new __ComponentProperty($component, 'text'), new __HtmlElementCallback($component->getId(), 'update'));
    }
    
    public function startRender(__IComponent &$component) {
        $properties = array();
        $properties[] = 'id = "' . $component->getId() . '"';
        if($component->getVisible() == false) {
            $properties[] = 'style = "display : none;"';
        }
        return '<div ' . join(' ', $properties) . '>';
    }
    
    public function endRender(__IComponent &$component)
    {
        return '</div>';
    }    
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        return $enclosed_content;
    }    
    
    
}
