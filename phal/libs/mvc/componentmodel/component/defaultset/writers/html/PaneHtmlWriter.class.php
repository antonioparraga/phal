<?php


class __PaneHtmlWriter extends __ComponentWriter {

    public function startRender(__IComponent &$component) {
        $properties = array();        
        $properties[] = 'id = "' . $component->getId() . '"';
        if($component->getVisible() == false) {
            $properties[] = 'style = "display : none;"';
        }
        $return_value = '<fieldset ' . $join(' ', $properties) . '">';
        if($component->getTitle() != null) {
            $return_value .= '<legend>' . $component->getTitle() . '</legend>';    
        }
        return $return_value;
    }
    
    public function endRender(__IComponent &$component)
    {
        return '</fieldset>';
    }    
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        return $enclosed_content;
    }    
    
    
}
