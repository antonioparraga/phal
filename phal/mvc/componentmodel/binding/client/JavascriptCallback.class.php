<?php

/**
 * Represents a callback to a javascript object's method
 *
 */
class __JavascriptCallback extends __ClientCallback {
      
    public function getClientCommandClass() {
        return '__ExecuteObjectCallbackCommand';
    }
      
}