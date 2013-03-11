<?php

class __CommandLineFrontController extends __FrontController {

    /**
     * This method dispatch the current request
     *
     */
    public function processRequest(__IRequest &$request, __IResponse &$response) {
        $action_identity = $request->getActionIdentity();
        $controller_code = $action_identity->getControllerCode();
        //in case we haven't define any controller, will use the commandline controller from phal admin:
        if(empty($controller_code)) {
            //execute the commandline controller:
            $action_identity->setControllerCode('commandline');
        }
        $controller_definition = __ActionControllerResolver::getInstance()->getActionControllerDefinition($action_identity->getControllerCode());
        //check if action controller is requestable
        if($controller_definition instanceof __ActionControllerDefinition && $controller_definition->isRequestable()) {
            __ActionDispatcher::getInstance()->dispatch( $action_identity );
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_ACTION_NON_REQUESTABLE');
        }
    }

    /**
     * Forward the execution flow to the given uri
     *
     * @param __Uri|string the uri (or an string representing the uri) to redirect to
     * @param __IRequest $request
     */
    public function forward($uri, __IRequest &$request = null) {
        if(is_string($uri)) {
            $uri = __UriFactory::getInstance()->createUri($uri);
        }
        else if(!$uri instanceof __Uri) {
            throw __ExceptionFactory::getInstance()->createException('Unexpected type for uri parameter: ' . get_class($uri));
        }
        if($request == null) {
            $request = __RequestFactory::getInstance()->createRequest();
        }
        $request->setUri($uri);
        $request->setRequestMethod(REQMETHOD_ALL);
        $response = __Client::getInstance()->getResponse();
        $response->clear(); //clear the response
        $this->dispatch($request, $response); //dispatch the request
        $response->flush(); //flush the response
        exit;
    }
    
    /**
     * Alias of forward
     *
     * @param __Uri|string the uri (or an string representing the uri) to redirect to
     * @param __IRequest $request
     */    
    public function redirect($uri, __IRequest &$request = null, $redirection_code = null) {
        $this->forward($uri, $request);
    }      
    
}