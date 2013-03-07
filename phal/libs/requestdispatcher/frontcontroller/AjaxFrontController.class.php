<?php

/**
 * This is the front controller designed to dispatch AJAX request due to ui events
 *
 */
class __AjaxFrontController extends __HttpFrontController {
    
    /**
     * This method process an AJAX request
     *
     */
    public function processRequest(__IRequest &$request, __IResponse &$response) {
        $ui_event = __EventResolver::getInstance()->resolveEvent();
        if($ui_event != null) {
            $view_code = $ui_event->getComponent()->getViewCode();
            $event_handler = __EventHandlerManager::getInstance()->getEventHandler($view_code);
            $event_handler->handleEvent($ui_event);
        }
    }

    /**
     * Redirect the web flow to the given uri
     *
     * @param __Uri|string the uri (or an string representing the uri) to redirect to
     * @param __IRequest &$request
     */
    public function redirect($uri, __IRequest &$request = null, $redirection_code = null) {
        if(is_string($uri)) {
            $uri = __UriFactory::getInstance()->createUri($uri);
        }
        else if(!$uri instanceof __Uri) {
            throw __ExceptionFactory::getInstance()->createException('Unexpected type for uri parameter: ' . get_class($uri));
        }
        $url = $uri->getUrl();
        $message = new __AsyncMessage();
        $message->getHeader()->setLocation($url);
        $this->getResponse()->addContent($message->toJson());
        $client_notificator = __ClientNotificator::getInstance();
        //notify to client:
        $client_notificator->notify();
        //clear dirty components to avoid notify again in next requests:
        $client_notificator->clearDirty();
    }
    
    
    /**
     * Alias of redirect
     *
     * @param __Uri|string the uri (or an string representing the uri) to redirect to
     * @param __IRequest &$request
     */
    public function forward($uri, __IRequest &$request = null) {
        $this->redirect($uri, $request);
    }
    
}