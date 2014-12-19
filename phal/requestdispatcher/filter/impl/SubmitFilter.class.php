<?php

class __SubmitFilter extends __Filter {
    
    public function preFilter(__IRequest &$request, __IResponse &$response) {
        if($request->hasParameter('submitCode') && $request->hasParameter('viewCode')) {
        	$view_code = $request->getParameter('viewCode');
        	$event_handler = __EventHandlerManager::getInstance()->getEventHandler($view_code);
        	if($event_handler != null) {
        		$service = $request->getParameter('submitCode') . '_submit';
        		if(method_exists($event_handler, $service)) {
        			call_user_func(array($event_handler, $service));
        		}
        	}       
        }
    }


}