<?php

class __ActionBoxHtmlWriter extends __ComponentWriter {
    
    public function startRender(__IComponent &$component) {
        $properties = array();
        return '<span id="' . $component->getId() . '" ' . join(" ", $properties) . '>';
    }
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        return $component->getContent();
    }
    
    public function endRender(__IComponent &$component) {
        return '</span>';
    }
    
}
