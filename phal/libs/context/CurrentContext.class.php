<?php

/**
 * Class to retrieve the current active context container
 *
 */
class __CurrentContext {
    
    /**
     * Return a reference to the current active {@link __Context} instance
     *
     * @return __Context The current active {@link __Context} instance
     */    
    static public function &getInstance() {
        return __ContextManager::getInstance()->getCurrentContext();
    }
    
}