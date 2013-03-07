<?php

class __BootstrapController extends __ActionController {
    
    public function defaultAction()
	{
        if( __ModelProxy::getInstance()->prepareAndValidateBootstrapEnvironment(APP_DIR) &&
        	__ModelProxy::getInstance()->doBootstrap(APP_DIR) )	{
        	echo "Congratullations, bootstrap done successfully!\n";
        }        
	}
	
	
}