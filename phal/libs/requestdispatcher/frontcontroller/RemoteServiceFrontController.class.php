<?php

/**
 * This is the front controller designed to dispatch remote service request
 *
 */
class __RemoteServiceFrontController extends __HttpFrontController {
    
    /**
     * This method process an AJAX request
     *
     */
    public function processRequest(__IRequest &$request, __IResponse &$response) {
        try {
            $return_value = $this->_resolveAndCallRemoteService($request);
            if($return_value !== null) {
                if(function_exists('json_encode')) {
                    $return_value = json_encode($return_value);
                }
                else {
                    $services_json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
                    $return_value = $services_json->encode($return_value);
                }
            }
            $response->addContent($return_value);
        }
        catch (Exception $e) {
            $response->addHeader("HTTP/1.0 500 Internal Server Error");
            $response->addContent(json_encode($e->getMessage()));
        }
    }
    
    protected function _resolveAndCallRemoteService(__IRequest &$request) {
        $return_value = null;
        $request = __FrontController::getInstance()->getRequest();
        if($request->hasParameter('service_name')) {
            $service_name = $request->getParameter('service_name');
            $model_proxy  = __ModelProxy::getInstance();
            if($model_proxy->isRemoteService($service_name)) {
                $model_service = $model_proxy->getModelService($service_name);
                $return_value  = $model_service->callAsRemoteService($request);
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('Service ' . $service_name . ' is not declared as remote');
            }
        }
        return $return_value;
    }
    
}