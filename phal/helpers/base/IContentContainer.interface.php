<?php

interface __IContentContainer {
    
    public function prependContent($content, $id = null);

    public function appendContent($content, $id = null);
    
    public function dockContentOnTop($content, $id = null);
    
    public function dockContentAtBottom($content, $id = null);
    
    public function placeContentAfterElement($content, $element, $id = null);
    
    public function addContent($content, $id = null, $after_content_id = null);
    
    public function clearContent($id = null);
    
    public function getContent();
    
}