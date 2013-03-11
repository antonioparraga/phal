<?php

class __RequiredFieldValidatorHtmlWriter extends __ValidatorHtmlWriter {

    public function startRender(__IComponent &$component) {
        
        
        
        return '<span id="' . $component->getId() . '" ' . join(" ", $properties) . '>';
    }    
    
}
