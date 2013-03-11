<?php

abstract class __FileLocator {
    
    protected $_search_dirs = array();
    
    final public function __construct() {
        $search_dirs = $this->getSearchDirs();
        foreach($search_dirs as $search_dir) {
            $this->addSearchDir($search_dir);
        }
    }
    
    final public function addSearchDir($search_dir) {
        $this->_search_dirs[] = $search_dir;
    }
    
    final public function reverseSearchDirs() {
        $this->_search_dirs = array_reverse($this->_search_dirs);
    }
    
    /**
     * Returns the path corresponding to a given file name
     *
     * @param string $file_name The file name
     * @return string The requested dirs
     */
	final public function locateFile($file_name) {
	    if($file_name != null) {
    	    foreach($this->_search_dirs as $search_dir) {
    	        $file_path = $search_dir . DIRECTORY_SEPARATOR . $file_name;
    	        if(is_readable($file_path) && is_file($file_path)) {
    	            return $file_path;
    	        }
    	    }
	    }
	    return null;
	}

	/**
	 * This method returns an array of all dirss to search
	 *
	 * @return array
	 */
	abstract public function getSearchDirs();
	
}