<?php

/**
 * This is the context container associated to our main application.
 *
 */
class __ApplicationContext {
    
    /**
     * Return a reference to the application {@link __Context} instance
     *
     * @return __Context The application {@link __Context} instance
     */
    static public function &getInstance() {
        return __ContextManager::getInstance()->getApplicationContext();
    }
    
    
}