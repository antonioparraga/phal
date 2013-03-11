<?php

/**
 * This is the default controller for command line requests.
 * It's executed in case no controllers have been specified on command line
 *
 */
class __CommandLineController extends __ActionController {
    
    protected function _readCommandLineRequest() {

    	$parser = new Console_CommandLine();
 	
    	$parser->description = "Phal Framework " . PHAL_VERSION_NUMBER . ' (built: ' . PHAL_VERSION_BUILD_DATE . ")\n" .
    						   "An open source PHP Framework for rapid development of PHP web applications";
    	$parser->version = PHAL_VERSION_NUMBER;
    	$parser->addOption(
    			'clearcache',
    			array(
    					'short_name'  => '-c',
    					'long_name'   => '--clearcache',
    					'description' => 'clear the cache',
    					'action'      => 'StoreTrue'
    			)
    	);
    	// Adding an option that will store a string
    	$parser->addOption(
    			'directives',
    			array(
    					'short_name'  => '-d',
    					'long_name'   => '--directives',
    					'description' => 'show the runtime directives',
    					'action'      => 'StoreTrue',
    			)
    	);    	
    	// Adding an option that will store a string
    	$parser->addOption(
    			'install',
    			array(
    					'short_name'  => '-i',
    					'long_name'   => '--install',
    					'description' => 'install phal and setup an empty structure of directories',
    					'action'      => 'StoreTrue',
    			)
    	);
    	// Adding an option that will store a string
    	$parser->addOption(
    			'controller',
    			array(
    					'long_name'   => '--controller',
    					'description' => 'executes the given controller',
    					'action'      => 'StoreString',
    			)
    	);
    	// Adding an option that will store a string
    	$parser->addOption(
    			'action',
    			array(
    					'long_name'   => '--action',
    					'description' => 'executes the given action',
    					'action'      => 'StoreString',
    			)
    	);
    	
    	return $parser;
    }
    
    public function defaultAction()	{

    	$options = array();
		$parser = $this->_readCommandLineRequest();
		try {
			$result = $parser->parse();
			$options = $result->options;
		} catch (Exception $exc) {
			$parser->displayError($exc->getMessage());
		}		
		

	    if($options['clearcache']) {
	    	__ApplicationContext::getInstance()->getSession()->destroy();
	    	__ApplicationContext::getInstance()->getCache()->clear();
            echo "Cache cleared!\n";
	    }
        else if($options['directives']) {
            $this->_printPhalInfo();
        }
        else if($options['install']) {
        	fwrite(STDOUT, "Where do you want to install your new application? (e.g. /var/www) : ");
        	$varin = trim(fgets(STDIN));        	
        	if(!empty($varin) && is_dir($varin) && is_writable($varin)) {
        		if(__ModelProxy::getInstance()->doBootstrap($varin)) {
        			echo "Installation completed!\n";
        		}
        	}
        	else {
        		print "ERROR: Either the directory that you have specified does not exists or I don't have permissions to write to...\n";
        	}
        }
        else {
        	$parser->displayUsage();
        }
	    
	}
	
    private function _printPhalInfo() {
        echo 'Phal framework ' . PHAL_VERSION_NUMBER . ' (built: ' . PHAL_VERSION_BUILD_DATE . ")\n";
        echo "\n";
        echo "Runtime Directives\n";
        echo "------------------\n";
        $phal_directives = __Phal::getInstance()->getRuntimeDirectives()->getDirectives();
        $runtime_directives_values = array();
        foreach($phal_directives as $key => $value) {
            if(is_bool($value)) {
                if($value) {
                    $value = 'true';
                }
                else {
                    $value = 'false';
                }
            }
            echo "$key: $value\n";
        }
        echo "\nApplication Settings\n";
        echo "--------------------\n";
        $configuration = __ApplicationContext::getInstance()->getConfiguration();
        $settings = $configuration->getSettings();
        $setting_values = array();
        foreach($settings as $key => $setting) {
            $value = $configuration->getPropertyContent($key);
            if(is_bool($value)) {
                if($value) {
                    $value = 'true';
                }
                else {
                    $value = 'false';
                }
            }
            
            echo "$key: $value\n";
        }
        
    }	
    
}