<?php

interface __IComponentWriter {

	public function bindComponentToClient(__IComponent &$component);
		
    public function startRender(__IComponent &$component);
    
    public function endRender(__IComponent &$component);
    
    public function renderContent($enclosed_content, __IComponent &$component);
    
    public function canRenderChildrenComponents(__IComponent &$component);
    
}