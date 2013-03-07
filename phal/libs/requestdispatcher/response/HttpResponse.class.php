<?php

class __HttpResponse extends __Response {
    
    protected $_headers = array();
    protected $_buffer_control = false;
    
    public function __construct() {
    }

    public function setBufferControl($buffer_control) {
        $this->_buffer_control = (bool) $buffer_control;
        if($this->_buffer_control) {
            ob_start(array($this, 'doFlush'));
        }
    }
    
    public function addHeader($header) {
        array_push($this->_headers, $header);
    }

    public function clearHeaders() {
        unset($this->_headers);
        $this->_headers = array();
    }
    
    public function flush() {
        if($this->_buffer_control) {
            @ob_end_flush();
            flush();
            ob_start(array($this, 'doFlush'));
        }
        else {
            print $this->doFlush();
        }
    }
    
    public function flushAll() {
        if($this->_buffer_control) {
            $level = ob_get_level();
            for($i = 0; $i < $level; $i++) {
                ob_end_flush();
            }
            flush();
        }
        else {
            print $this->doFlush();
        }
    }    
    
    public function doFlush($buffer = null) {
        //add the pending buffer to the current response content (if not empty)
        if(!empty($buffer)) {
            $this->appendContent($buffer);
        }
        if(!headers_sent()) {
            foreach($this->_headers as $header) {
                header($header);
            }
        }
        $content = $this->getContent();
        $this->clearContent();
        return $content;
    }
    
    public function __toString() {
        return $this->getContent();
    }
    
    public function addCookie(__Cookie $cookie)
    {   
        $cookie_array = array($cookie->getName(), 
                              $cookie->getValue(),
                              $cookie->getTtl(),
                              $cookie->getPath(),
                              $cookie->getDomain(),
                              $cookie->getSecure(),
                              $cookie->getHttpOnly());
                              
        if($cookie->useUrlEncoding()) {
            call_user_func_array('setcookie', $cookie_array);
        }
        else {
            call_user_func_array('setrawcookie', $cookie_array);
        }
    }
    
    public function addCookies(array $cookies) {
        foreach($cookies as $cookie) {
            $this->addCookie($cookie);
        }
    }
    
    
}

