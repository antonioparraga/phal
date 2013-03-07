<?php

/**
 * This is the factory for {@link __CacheHandler}.
 *
 */
final class __CacheHandlerFactory {

    /**
     * Creates a new cache handler (a class implementing the {@link __ICacheHandler}) based on context configuration.
     *
     * @return __CacheHandler
     */
    static final public function &createCacheHandler() {
        $return_value = null;
        $cache_handler_class = __Phal::getInstance()->getRuntimeDirectives()->getDirective('CACHE_HANDLER_CLASS');
        if(!empty($cache_handler_class)) {
            $cache_impl_dir = PHAL_CACHE_DIR . DIRECTORY_SEPARATOR . 'impl';
            if(!class_exists($cache_handler_class)) {
                switch($cache_handler_class) {
                    case '__Apc':
                        $cache_impl_file = $cache_impl_dir . DIRECTORY_SEPARATOR . 'Apc.class.php';
                        break;
                    case '__CacheLite':
                        $cache_impl_file = $cache_impl_dir . DIRECTORY_SEPARATOR . 'CacheLite.class.php';
                        break;
                    case '__MemCache':
                        $cache_impl_file = $cache_impl_dir . DIRECTORY_SEPARATOR . 'MemCache.class.php';
                        break;
                    case '__MemCached':
                        $cache_impl_file = $cache_impl_dir . DIRECTORY_SEPARATOR . 'MemCached.class.php';
                        break;
                    default:
                        $cache_impl_file = $cache_impl_dir . DIRECTORY_SEPARATOR . __Phal::getInstance()->getRuntimeDirectives()->getDirective('CACHE_HANDLER_FILE');
                        break;
                }
                include_once($cache_impl_file);
            }
            
            $return_value = new $cache_handler_class();
            if (! $return_value instanceof __ICacheHandler ) {
                throw new Exception('Wrong cache handler class: ' . $cache_handler_class . '. A class implementing the __ICacheHandler was expected.');
            }
            
        }
        return $return_value;
    }
    
    
}