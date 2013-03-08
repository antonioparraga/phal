<?php

class __PhalAdminFilter extends __Filter {
    
    /**
     * Switch the context to phal admin application, just in case the REQUEST_PHAL_ADMIN_AREA parameter is present within the request
     *
     * @param __IRequest $request
     * @param __IResponse $response
     */
    public function preFilter(__IRequest &$request, __IResponse &$response) {
        $admin_area_parameter = __ApplicationContext::getInstance()->getPropertyContent('REQUEST_PHAL_ADMIN_AREA');
        if($request->hasParameter($admin_area_parameter) && __ApplicationContext::getInstance()->getPropertyContent('PHAL_ADMIN_ENABLED') == true) {
            __ContextManager::getInstance()->createContext("PHAL_ADMIN_AREA", ADMIN_DIR);
            __ContextManager::getInstance()->switchContext("PHAL_ADMIN_AREA");
        }
    }
    
}