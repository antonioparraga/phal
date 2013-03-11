<?php

interface __IResponse extends __IContentContainer {
    
    public function flush();
    
    public function __toString();
    
}