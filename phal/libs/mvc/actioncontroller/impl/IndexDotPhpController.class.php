<?php

/**
 * This is the controller executed when requesting the index.php file from the browser.
 * This controller will check the server configuration and will alert about whatever problem found.
 * 
 * In case the configuration is ok, will advice about the usage of beautifull urls instead of executing the index.php
 *
 */
class __IndexDotPhpController extends __ActionController {

    public function defaultAction() {
        if(__Phal::getInstance()->getRuntimeDirectives()->getDirective('DEBUG_MODE')) {
            //perform the validation:
            __ServerConfigurationValidator::validate();
            $url = __UriFactory::getInstance()->createUri()->setController('index')->getAbsoluteUrl();
            $file = basename($url);
            $baseurl = str_replace($file, '', $url);
            
            $message = <<<CODESET

<h1>Do not execute index.php from your browser</h1><br>
Phal intercepts all requests in the form of <b>$baseurl...</b> and redirects them to the MVC in order to show the page corresponding to each one.<br>
i.e. you may use $url, which will be intercepted by phal and redirected to the index controller<br> 
<br>
Go to <a href="$url">$file</a><br>
<br>
<br>
CODESET;
            echo $message;            
        }
    }
    
}
