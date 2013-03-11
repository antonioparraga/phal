<?php

class __ConfigurationStreamWrapper extends __StreamWrapper {

    /**
     * setup the current __ConfigurationStreamWrapper.
     * 
     * __ConfigurationStreamWrapper url format: config://[application_id/]config_id<br>
     * i.e. config://url_routing.xml    - retrieve the url_routing.xml file
     *
     * @param string $path
     */
    protected function &_createStreamStorage($path) {
        throw new __ConfigurationException('Can not access to the ' . $path . ' file because the config stream has been deprecated. Please use relative paths to your configuration files (i.e. instead of config://settings.ini use config/settings.ini)');
    }
    

}