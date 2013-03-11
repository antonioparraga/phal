<?php

class __ErrorController extends __ActionController {

    protected function &defaultAction()
    {
        $request = __ActionDispatcher::getInstance()->getRequest();
        $error_parameters = array();
        //1. If REQUEST_ERROR_CODE has been found on request:
        if( $request->hasParameter(__ApplicationContext::getInstance()->getPropertyContent('REQUEST_ERROR_CODE'))) {
            $error_code = $request->getParameter(__ApplicationContext::getInstance()->getPropertyContent('REQUEST_ERROR_CODE'));
            switch($error_code) {
                case 55601:
                    $error_parameters[] = $request->getUri()->getAbsoluteUrl();
                    break;
            }
        }
        //2. Else we don't know the exception to show (use the unknow exception):
        else {
            $error_code = __ExceptionFactory::getInstance()->getErrorTable()->getErrorCode('ERR_UNKNOW_ERROR');
        }
        throw __ExceptionFactory::getInstance()->createException($error_code, $error_parameters);
    }
}
