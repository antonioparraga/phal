<?php


/**
 * This is an optimized Front Controller for handling resource requests.
 * 
 * It accelerate the response by avoiding certain unnecessary operations for a resource request (like validations, permissions checking, ...)
 *
 */
class __ResourceFrontController extends __HttpFrontController {
    
    public function processRequest(__IRequest &$request, __IResponse &$response) {
        __ActionDispatcher::getInstance()->dispatch(new __ActionIdentity('resource'));
    }
   
}
