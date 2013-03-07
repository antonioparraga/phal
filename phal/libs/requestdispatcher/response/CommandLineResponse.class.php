<?php

class __CommandLineResponse extends __Response {
    
    public function flush() {
        echo $this->getContent();
    }
    
    public function __toString() {
        return $this->getContent();
    }    
    
}