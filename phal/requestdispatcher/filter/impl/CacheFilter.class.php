<?php

/**
 * This filter will try to get the response from the cache (aka front controller cache or page level cache)
 * If success, won't call the rest of the filter chain, returning the cached response.
 * Otherwise it will cache the response for further requests.
 * 
 * This filter only work in routes marked as cache="yes"
 * 
 *
 */
class __CacheFilter extends __Filter {
    
    public function execute(__IRequest &$request, __IResponse &$response, __FilterChain &$filter_chain) {
        $response_from_cache = $this->_getResponseFromCache($request, $response);
        if($response_from_cache == null) {
        	$filter_chain->execute($request, $response);
        	$this->_setResponseToCache($request, $response);
        }
        else {      	
            $response =& $response_from_cache;
        }
        
    }

    protected function _getResponseFromCache(__IRequest &$request, __IResponse &$response) {
        $return_value = null;
        $uri = $request->getUri();
        if($uri != null) {
            $route = $uri->getRoute();
            if($route != null && $route->getCache()) {
                //only use cache version of anonymous view:
                if(__AuthenticationManager::getInstance()->isAnonymous()) {
                    $cache = __ApplicationContext::getInstance()->getCache();
                    $response_content = $cache->getData('__CacheFilter::' . $request->getUniqueCode(), $route->getCacheTtl());
                    if($response_content != null) {
                    	$response->clear();
                        $response->addContent($response_content);
                        $response->setBufferControl(true);
                        $return_value = $response;
                    }
                }
            }
        }
        return $return_value;
    }

    
    protected function _setResponseToCache(__IRequest &$request, __IResponse &$response) {
        $uri = $request->getUri();
        if($uri != null) {
            $route = $uri->getRoute();
            if($route != null) {
                if($route->getCache()) {
                    //only cache anonymous view:
                    if($response->isCacheable()) {
                    	$cache = __ApplicationContext::getInstance()->getCache();
                    	$response_content = $response->getContent() . "\n<!-- cached -->";
                    	$cache->setData('__CacheFilter::' . $request->getUniqueCode(), $response_content, $route->getCacheTtl());
                    }
                }
                else if($route->getSuperCache()) {
                    //only cache anonymous view:
                    if($response->isCacheable()) {
                        $target_url_components = parse_url($uri->getAbsoluteUrl());
                        if($route->getSuperCacheFile() != null) {
                        	$path = $route->getSuperCacheFile();
                        }
                        else {
                        	$path = $target_url_components['path'];
                        }
                        $dir = dirname($path);
                        if($dir == '.') {
                        	$dir = '';
                        }
                        $file = basename($path);
                        $response_content = $response->getContent() . "\n<!-- supercached -->";
                        $cache_ttl = $route->getCacheTtl();
                        $server_dir = $_SERVER['DOCUMENT_ROOT'] . $dir;
                        if(is_dir($server_dir) && is_writable($server_dir)) {
                            $file_handler = fopen($server_dir . DIRECTORY_SEPARATOR . $file, "w+");
                            fputs($file_handler, $response_content);
                            fclose($file_handler);
                        }
                        else {
                            $exception = __ExceptionFactory::getInstance()->createException('Directory not found to supercache: ' . $server_dir);
                            __ErrorHandler::getInstance()->logException($exception);
                        }
                    }
                }
            }
        }
    }
        
    
}
