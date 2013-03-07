<?php

class __CacheController extends __ActionController {
    
    public function clearAction() {
        __ApplicationContext::getInstance()->getSession()->destroy();
        __ModelProxy::getInstance()->clearCache();
        $mav = new __ModelAndView('confirmation');
        $mav->title = 'Cache cleared!';
        return $mav; 
    }
    
}
