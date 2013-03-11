<?php

class __PlainContentRenderer implements __IRenderer {

    protected $_plain_content = null;
    
    public function __construct($plain_content) {
        $this->_plain_content = $plain_content;
    }
    
    public function render() {
        return $this->_plain_content;
    }
    
    
}