<?php

/**
 * This is the most basic implementation to dispatch HTTP requests.
 *
 */
class __HttpFrontController extends __FrontController {

    /**
     * This method dispatch the current request
     * 
     * @param __IRequest &$request The request to process to
     * @param __IResponse &$response The instance to set the response to
     *
     */
    public function processRequest(__IRequest &$request, __IResponse &$response) {
        //resolve action identity from request
        $action_identity = $request->getActionIdentity();
        //resolve the action controller associated to the given action identity
        $controller_definition = __ActionControllerResolver::getInstance()->getActionControllerDefinition($action_identity->getControllerCode());
        //check if action controller is requestable
        if($controller_definition instanceof __ActionControllerDefinition && $controller_definition->isRequestable()) {
            __HistoryManager::getInstance()->addRequest($request);
            //last, execute the action controller
            __ActionDispatcher::getInstance()->dispatch($action_identity);
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_ACTION_NON_REQUESTABLE');
        }
    }
    
    /**
     * Forward the web flow to the given uri.
     * This method is similar to redirect, but performs the redirection internally (without http redirection codes)
     *
     * @param __Uri|string the uri (or an string representing the url) to redirect to
     * @param __IRequest &$request The request instance to use in the forward
     * 
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
     * Redirects to the given uri.
     * This method uses an HTTP redirection to force the client browser to be redirected to
     * 
     * @see __HttpFrontController::forward()
     *
     * @param __Uri|string the uri (or an string representing the uri) to redirect to
     * @param __IRequest &$request The request associated to the redirection
     * @param integer $redirection_code The HTTP redirection code
     */
    public function redirect($url, __IRequest &$request = null, $redirection_code = null) {
        if($url instanceof __Uri) {
    		$url = $url->getUrl();
        }
        else if(!is_string($url)) {
            throw __ExceptionFactory::getInstance()->createException('Unexpected type for url parameter: ' . get_class($url));
        }
        if($request != null) {
            $parameters = $request->getParameters(REQMETHOD_GET);
            if(count($parameters) > 0) {
                $query_string = http_build_query($parameters);
                if(!empty($query_string)) {
                    if(strpos($url, '?') === false) {
                        $url .= '?';
                    }
                    else {
                        $url .= '&';
                    }
                    $url .= $query_string;
                }
            }
        }
        //Now will redirect the user to show the error:
        if (!headers_sent()) {
            switch($redirection_code) {
                case 300:
                    header("HTTP/1.1 300 Multiple Choices");
                    break;
                case 301:
                    header("HTTP/1.1 301 Moved Permanently");
                    break;
                case 302:
                    header("HTTP/1.1 302 Found");
                    break;
                case 303:
                    header("HTTP/1.1 303 See Other");
                    break;
                case 304:
                    header("HTTP/1.1 304 Not Modified");
                    break;
                case 305:
                    header("HTTP/1.1 305 Use Proxy");
                    break;
                case 306:
                    header("HTTP/1.1 306 Switch Proxy");
                    break;
                case 307:
                    header("HTTP/1.1 307 Temporary Redirect");
                    break;
                default:
                    //nothing to do
                    break;
            }
            header('Location: ' . $url);
        } else {
            print '
    <SCRIPT LANGUAGE=JAVASCRIPT>
      document.location.href = "' . $url . '";
    </SCRIPT>
        ';
        }
        exit;
    }    
    
}