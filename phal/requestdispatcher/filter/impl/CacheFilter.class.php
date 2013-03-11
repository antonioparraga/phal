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

        $response_from_cache = $this->_getResponseFromCache($request);
        if($response_from_cache == null) {
            $filter_chain->execute($request, $response);
            $this->_setResponseToCache($request, $response);
        }
        else {
            $response =& $response_from_cache;
        }
        
    }

    protected function _getResponseFromCache(__IRequest &$request) {
        $return_value = null;
        $uri = $request->getUri();
        if($uri != null) {
            $route = $uri->getRoute();
            if($route != null && $route->getCache()) {
                //only use cache version of anonymous view:
                if(__AuthenticationManager::getInstance()->isAnonymous()) {
                    $cache = __ApplicationContext::getInstance()->getCache();
                    $response_snapshot = $cache->getData('responseSnapshot::' . $request->getUniqueCode(), $route->getCacheTtl());
                    if($response_snapshot != null && $response_snapshot->areViewsRestorable()) {
                        $return_value = $response_snapshot->getResponse();
                        if($return_value instanceof __HttpResponse) {
                            $return_value->setBufferControl(true);
                        }
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
                        $response_snapshot = new __ResponseSnapshot($response);
                        $cache = __ApplicationContext::getInstance()->getCache();
                        $cache->setData('responseSnapshot::' . $request->getUniqueCode(), $response_snapshot, $route->getCacheTtl());
                    }
                }
                else if($route->getSuperCache()) {
                    //only cache anonymous view:
                    if($response->isCacheable()) {
                        $target_url_components = parse_url($uri->getAbsoluteUrl());
                        $path = $target_url_components['path'];
                        $dir = dirname($path);
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
