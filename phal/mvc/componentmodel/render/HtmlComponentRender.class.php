<?php

class __HtmlComponentRender extends __ComponentRenderEngine {

    public function startRender() {
        
        if(__FrontController::getInstance()->getRequestType() != REQUEST_TYPE_XMLHTTP) {
            if(__ApplicationContext::getInstance()->hasProperty('INCLUDE_PHAL_JS')) {
                $include_phal_js = __ApplicationContext::getInstance()->getPropertyContent('INCLUDE_PHAL_JS');
            }
            else {
                $include_phal_js = true;
            }
            if($include_phal_js) {
                $local_js_lib = __ApplicationContext::getInstance()->getPropertyContent('JS_LIB_DIR');
                $phal_js_file = __UrlHelper::resolveUrl(__UrlHelper::glueUrlParts($local_js_lib, 'phal.js'));
                __FrontController::getInstance()->getResponse()->prependContent('<script language="javascript" type="text/javascript" src="' . $phal_js_file . '"></script>' . "\n", 'phal-js');
            }
        }

        $response_writer_manager = __ResponseWriterManager::getInstance();
        if($response_writer_manager->hasResponseWriter('javascript')) {
            $javascript_response_writer = $response_writer_manager->getResponseWriter('javascript');
        }
        else {
            $javascript_response_writer = new __JavascriptOnDemandResponseWriter('javascript');
            $response_writer_manager->addResponseWriter($javascript_response_writer);
        }
        
        if(!$javascript_response_writer->hasResponseWriter('setup-client-event-handler')) {
            $setup_client_event_handler_rw = new __JavascriptOnDemandResponseWriter('setup-client-event-handler');
            $js_code = "\n" . '(__ClientEventHandler.getInstance()).setCode("' . __CurrentContext::getInstance()->getId() . '");' . "\n";
            if(__Phal::getInstance()->getRuntimeDirectives()->getDirective('DEBUG_MODE')) {
                $js_code .= "(__ClientEventHandler.getInstance()).setDebug(true);\n";
                if(__ApplicationContext::getInstance()->getPropertyContent('DEBUG_AJAX_CALLS') == true) {
                    if(strtoupper(__ApplicationContext::getInstance()->getPropertyContent('DEBUGGER')) == 'ZEND') {
                        $client_ip  = $_SERVER['REMOTE_ADDR'];
                        $debug_port = __ApplicationContext::getInstance()->getPropertyContent('ZEND_DEBUG_PORT');
                        $debug_url  = 'index.ajax?' . 'start_debug=1&debug_port=' . $debug_port . '&debug_fastfile=1&debug_host=' . $client_ip . '&send_sess_end=1&debug_stop=1&debug_url=1&debug_new_session=1&no_remote=1';
                        $js_code .= "(__ClientEventHandler.getInstance()).setUrl('" . $debug_url . "');\n";
                    }
                }
            }
            if(!__FrontController::getInstance() instanceof __ComponentLazyLoaderFrontController &&
                __FrontController::getInstance()->getRequestType() == REQUEST_TYPE_HTTP) {
                $url = __FrontController::getInstance()->getRequest()->getUrl();
                $encoded_url = base64_encode(serialize($url));
                $js_code .= "(__ClientEventHandler.getInstance()).setViewCode('" . $encoded_url . "');\n";
                $flow_scope = __ApplicationContext::getInstance()->getFlowScope();
                if($flow_scope != null) {
                    $js_code .= "(__ClientEventHandler.getInstance()).setFlowExecutionKey('" . $flow_scope->getId() . "');\n";
                }
            }
            $setup_client_event_handler_rw->addJsCode($js_code);
            $javascript_response_writer->addResponseWriter($setup_client_event_handler_rw);
        }
        parent::startRender();
    }
    
    public function endRender() {
        parent::endRender();

        $async_message = __ClientNotificator::getInstance()->getStartupNotification($this->_view_code);
        if($async_message != null && ($async_message->getHeader()->getStatus() != __AsyncMessageHeader::ASYNC_MESSAGE_STATUS_OK || $async_message->hasPayload())) {
            $response_writer_manager = __ResponseWriterManager::getInstance();
            if($response_writer_manager->hasResponseWriter('javascript')) {
                $javascript_response_writer = $response_writer_manager->getResponseWriter('javascript');
            }
            else {
                $javascript_response_writer = new __JavascriptOnDemandResponseWriter('javascript');
                $response_writer_manager->addResponseWriter($javascript_response_writer);
            }
            if($response_writer_manager->hasResponseWriter('setup-client-view')) {
                $setup_client_view_rw = $response_writer_manager->getResponseWriter('setup-client-view');
            }
            else {
                $setup_client_view_rw = new __JavascriptOnDemandResponseWriter('setup-client-view');
                $setup_client_view_rw->setLoadAfterDomLoaded(false);
                $javascript_response_writer->addResponseWriter($setup_client_view_rw);
            }
            $javascript_response_writer->addJsCode('__MessageProcessor.process(new __Message(' . $async_message->toJson() . '));', __JavascriptOnDemandResponseWriter::JS_CODE_POSITION_BOTTOM);
        }
    }

}

