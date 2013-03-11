<?php

@ini_set("zlib.output_compression", "Off");

class __ResourceController extends __ActionController {

    public function &defaultAction() {
        return $this->resourceAction();
    }
    
    public function &resourceAction() {
    	$model_and_view  =  new __ModelAndView('resource');
        $resource_to_load = __ActionDispatcher::getInstance()->getRequest()->getParameter('resource');
        $model_and_view->addObject('resource', $resource_to_load);
    	return $model_and_view;
    }
    
    public function &bresourceAction() {
    	$model_and_view  =  new __ModelAndView('resource');
	    $resource_id = __ActionDispatcher::getInstance()->getRequest()->getParameter('resource_id');
	    $model_and_view->addObject('resource_id', $resource_id);
    	return $model_and_view;
    }
	
}
