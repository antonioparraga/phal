<?php

class __ComponentLazyLoaderFrontController extends __HttpFrontController {

    public function dispatch(__IRequest &$request, __IResponse &$response) {
        $front_controller = self::$_instance;
        self::$_instance = $this;
        //now call the parent dispatch method:
        parent::dispatch($request, $response);
        //finally restore the previous front controller singleton
        self::$_instance = $front_controller;
        //and clear the response writer:
        __ResponseWriterManager::getInstance()->clear();
        __ClientNotificator::getInstance()->clearDirty();
        @ob_clean(); //clear the output buffer
    }
    
}
