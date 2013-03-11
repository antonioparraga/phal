<?php


class __ResourceView extends __SimpleView {

    public function execute() {
        $resource = $this->_getResourceToRender();
        if($resource != null) {
            $resource->display();        
        }
    }
    
    protected function _getResourceToRender() {
        $return_value = null;
    	if($this->isAssigned('resource')) {
            $resource_to_load = $this->getAssignedVar('resource');
            if( $this->_isValidResource($resource_to_load) ) {
                if(file_exists($resource_to_load) && strpos(realpath($resource_to_load), APP_DIR) !== false) {
                    $resource_content = file_get_contents($resource_to_load);
                }
                else {
        	        $resource_content = file_get_contents(PHAL_DIR . DIRECTORY_SEPARATOR . $resource_to_load);
                }
        	    $return_value = new __FileResource();
        	    $return_value->setValue($resource_content);
        	    $return_value->setFileName($resource_to_load);
            }
    	}
    	else if($this->isAssigned('resource_id')) {
    	    $resource_id     = $this->getAssignedVar('resource_id');
    	    $return_value    = __Resourcemanager::getInstance()->getResource($resource_id);
    	    __ResourceManager::getInstance()->removeResource($resource_id);
    	}
    	return $return_value;        
    }    
    
	protected function _isValidResource($resource_to_load) {
	    $return_value = false; //by default is not valid
        if(strpos($resource_to_load, '..') === false &&
           preg_match('/(\.gif|\.jpg|\.png|\.css|\.js|\.htm|\.html)$/i', $resource_to_load)) {
            $return_value = true;
        }
	    return $return_value;
	}    
    
}

