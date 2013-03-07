<?php

/**
 * This is the front controller designed to handle asynchronous file uploads
 *
 */
class __AsyncUploadFrontController extends __HttpFrontController {
    
    public function processRequest(__IRequest &$request, __IResponse &$response) {
        //do nothing (everything was done by the submit filter)
    }
    
}