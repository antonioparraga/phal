<?php

class __ComponentCallbackController extends __ActionController {
    
    public function defaultAction() {
        $request = __ActionDispatcher::getInstance()->getRequest();
        if($request->hasParameter('component_id')) {
            $component_id = $request->getParameter('component_id');
            $component_pool = __ComponentPool::getInstance();
            if($component_pool->hasComponent($component_id)) {
                $component = $component_pool->getComponent($component_id);
                $response = $component->handleCallback($request);
                print $response;
            }
        }
    }
    
}