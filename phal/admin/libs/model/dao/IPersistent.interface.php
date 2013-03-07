<?php

interface __IPersistent {
    
    public function setTransient($transient);
    
    public function isTransient();
    
    public function isDirty();
    
    public function setDirty($dirty);
        
}