<?php

class __BootstrapController extends __ActionController {
    
	private $_minimum_environment_files = array('.htaccess', 'index.php', 'var');
	
    public function defaultAction()
	{
        if( $this->prepareAndValidateBootstrapEnvironment(APP_DIR) &&
        	$this->doBootstrap(APP_DIR) )	{
        	echo "Congratullations, bootstrap done successfully!\n";
        }        
	}
	
	/**
	 * This method do a installation of a new Phal application on the specified location
	 *
	 * @param string $bootstrap_location where to do the bootstrap
	 */
	public function doBootstrap($bootstrap_location) {
		$return_value = false; //by default
		if($this->_validateEnvironment() && $this->_validateBootstrapLocation($bootstrap_location)) {
			try {
				$this->_copy_tree(SANDBOX_DIR, $bootstrap_location);
				$return_value = true;
			}
			catch (Exception $e) {
				//if any exception is raised, will rollback the bootstrap and
				$this->_rollbackBootstrap($bootstrap_location);
				//re
				throw $e;
			}
		}
		return $return_value;
	}
	
	private function _copy_tree( $source, $target ) {
		$permissions = array();
		if ( is_dir( $source )) {
			if(!file_exists( $target )) {
				if(mkdir( $target, 0755 ) === false) {
					throw __ExceptionFactory::getInstance()->createException('The directory ' . $target . DIRECTORY_SEPARATOR . $entry . ' is not writable. Please fix the permissions in order to perform the installation.');
				}
			}
			$d = dir( $source );
			while ( false !== ( $entry = $d->read() ) ) {
				if ( $entry != '.' && $entry != '..' ) {
					if ( is_dir( $source . DIRECTORY_SEPARATOR . $entry ) ) {
						if(!file_exists( $target . DIRECTORY_SEPARATOR . $entry ) && mkdir( $target . DIRECTORY_SEPARATOR . $entry, 0755 ) === false) {
							throw __ExceptionFactory::getInstance()->createException('The directory ' . $target . DIRECTORY_SEPARATOR . $entry . ' is not writable. Please fix the permissions in order to perform the installation.');
						}
						$this->_copy_tree( $source . DIRECTORY_SEPARATOR . $entry, $target . DIRECTORY_SEPARATOR . $entry );
						continue;
					}
					else {
						if( preg_match('/^mode\.(\d+)$/', $entry, $permissions ) ) {
							chmod($target, intval($permissions[1], 8));
						}
						else if ($entry != 'create.me') {
							$this->_copy_file($source . DIRECTORY_SEPARATOR . $entry, $target . DIRECTORY_SEPARATOR . $entry);
						}
					}
				}
			}
			$d->close();
		}
		else if (is_file($source)) {
			$this->_copy_file($source, $target);
		}
	}
	
	private function _copy_file($source, $target) {
		if(is_file($source)) {
			$file = basename($source);
			$basedir = dirname($source);
			if( copy( $source, $target ) === false ) {
				throw __ExceptionFactory::getInstance()->createException('Error while copying the ' . $file . ' file to the target directory while installing the application.');
			}
		}
	}
	
	private function _rollbackBootstrap($boostrap_location) {
		$d = dir( $boostrap_location );
		while ( false !== ( $entry = $d->read() ) ) {
			if ( $entry != '.' && $entry != '..' && $entry != 'bootstrap.php') {
				//           unlink($entry);
			}
		}
		$d->close();
	}
	
	/**
	 * Validates phal framework environment requirements and add the .htaccess to
	 * help redirectiong the resource route to the framework during the installation process
	 *
	 * @param string $bootstrap_location
	 * @return bool true if success
	 */
	public function prepareAndValidateBootstrapEnvironment($bootstrap_location) {
		$return_value = false; //by default
		if($this->_validateEnvironment() && $this->_validateBootstrapLocation($bootstrap_location)) {
			try {
				$this->_prepareEnvironment($bootstrap_location);
				$return_value = true;
			}
			catch (Exception $e) {
				$this->_rollbackBootstrap($bootstrap_location);
				throw $e;
			}
		}
		return $return_value;
	}
	
	private function _prepareEnvironment($bootstrap_location) {
		foreach($this->_minimum_environment_files as $file) {
			$source_file = SANDBOX_DIR . DIRECTORY_SEPARATOR . $file;
			$target_file = $bootstrap_location . DIRECTORY_SEPARATOR .  $file;
			$this->_copy_tree($source_file, $target_file);
		}
	}
	
	/**
	 * Validates the environment before perform the bootstrap.
	 *
	 * - validates that mod_rewrite is enabled
	 * - validates that php-domxml extenssion is disabled
	 *
	 * @return bool true if the environment is valid to perform the bootstrap
	 */
	private function _validateEnvironment() {
		$php_extensions = get_loaded_extensions();
		if(in_array('domxml', $php_extensions) || in_array('php_domxml', $php_extensions)) {
			throw __ExceptionFactory::getInstance()->createException('php_domxml extension detected and need to be disabled.');
		}
		return true;
	}
	
	private function _validateBootstrapLocation($bootstrap_location) {
		//check if it's a directory:
		if(!is_dir($bootstrap_location) || !is_readable($bootstrap_location)) {
			throw __ExceptionFactory::getInstance()->createException('The specified location (' . $bootstrap_location . ') is not a valid directory or I don\'t the appropriate permissions to write to. Please ensure that the target is a writable directory.');
		}
		//check if the target directory just contains the bootstrap.php file:
		return true;
	}
		
	
}