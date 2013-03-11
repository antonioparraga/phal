<?php

/**
 * Represents a callback to a client element method, being the client element a dom node
 *
 */
class __HtmlElementCallback extends __ClientCallback {
        
    public function getClientCommandClass() {
        return '__ExecuteElementCallbackCommand';
    }
    
}