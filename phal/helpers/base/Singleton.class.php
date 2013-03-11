<?php

/**
 * Generic Singleton base class usefull to retrieve a singleton instance from the current context.
 * <br>
 * Just inherit(extend) from __Singleton and add a getter like the following one:
 * <br>
 * <code>
 * public static function &getInstance() {
 *   return __Singleton::getSingleton(AN_INSTANCE_ID);
 * }
 * </code>
 * <br>
 * Note that you need to specify an instance id to retrieve the singleton instance from the current context.<br>
 * The getSingleton static method will call to the getInstance method of the current {@link __Context} instance
 * 
 */
abstract class __Singleton {
    
    /**
     * Protected getter for singleton instances
     *
     * @param string $instance_id The instance id to retrieve the instance from current {@link __Context} instance
     * @return object The requested instance
     */
    protected static function &getSingleton($instance_id){
        $return_value = null;
        if (__ContextManager::getInstance()->getCurrentContext()->hasInstance($instance_id)) {
            $return_value = __ContextManager::getInstance()->getCurrentContext()->getInstance($instance_id);
        }
        return $return_value;
    }
    
    /**
     * Denie cloning of singleton objects
     *
     */
    public final function __clone(){
        throw __ExceptionFactory::getInstance()->createException('Clone is not allowed for a __Singleton class');
    }    
}
    
