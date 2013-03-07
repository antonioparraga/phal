<?php

class __TestResponseController extends __ActionController {

    /**
     * This action just output an "OK".
     * It's used by the bootstrap environment validation process in order to check that
     * rewrite engine is working as expected.
     *
     */
    public function defaultAction() {
        echo 'OK';
        exit;
    }
    
}
