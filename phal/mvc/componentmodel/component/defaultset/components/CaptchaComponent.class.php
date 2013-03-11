<?php

class __CaptchaComponent extends __UIComponent implements __IPoolable {

    public function isEventHandled($event_name) {
        $return_value = false;
        if(strtoupper($event_name) == 'CALLBACK') {
            $return_value = true;
        }
        return $return_value;
    }
    
    public function getHandledEvents() {
        return array('callback');
    }
    
    public function handleEvent(__UIEvent &$event) {
        $img = new securimage();
        $img->show();        
    }
    
    public function check($value) {
        $img = new Securimage();
        $return_value = $img->check($value);
        return $return_value;
    }
    
}
