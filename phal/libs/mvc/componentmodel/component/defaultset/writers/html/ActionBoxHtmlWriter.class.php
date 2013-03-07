<?php

class __ActionBoxHtmlWriter extends __ComponentWriter {

	public function bindComponentToClient(__IComponent &$component) {
	    $sep = new __ComponentProperty($component, 'response');
	    $cep = new __HtmlElementCallback($component->getId(), 'update');
	    $cep->setSynchronizationPrefilterCallback(new __Callback($component, 'isUnsynchronized'));
        __UIBindingManager::getInstance()->bindFromServerToClient($sep, $cep);
    }
    
    public function startRender(__IComponent &$component) {
        $properties = array();
        if($component->getVisible() == false) {
            $properties[] = 'style = "display : none;"';
        }
        return '<span id="' . $component->getId() . '" ' . join(" ", $properties) . '>';
    }
    
    public function renderContent($enclosed_content, __IComponent &$component) {
        return $component->getContent();
    }
    
    public function endRender(__IComponent &$component) {
        return '</span>';
    }
    
}
