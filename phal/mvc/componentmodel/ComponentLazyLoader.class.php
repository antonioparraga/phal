<?php

class __ComponentLazyLoader {

    static public function loadView($view_code) {

        try {
            $url = unserialize(base64_decode($view_code));
            $request = __RequestFactory::getInstance()->createRequest(REQUEST_TYPE_HTTP);
            $response = __ResponseFactory::getInstance()->createResponse(REQUEST_TYPE_HTTP);
            $url_components = parse_url($url);
            if(is_array($url_components) && key_exists('query', $url_components)) {
                $query = $url_components['query'];
                $get_pairs  = explode('&', $query);
                foreach($get_pairs as $get_pair) {
                    $get_pair_array = explode('=', $get_pair);
                    $request->addParameter($get_pair_array[0], $get_pair_array[1], REQMETHOD_GET);
                }
            }
            $uri = __UriFactory::getInstance()->createUri($url_components['path']);
            $request->setUri($uri);
            $request->setRequestMethod(REQMETHOD_GET);
            $front_controller = new __ComponentLazyLoaderFrontController();                            
            $front_controller->dispatch($request, $response); //dispatch the request
        }
        catch(Exception $e) {
            __ExceptionFactory::getInstance()->createException('Can not load view for view code ' . $view_code . ': ' . $e->getMessage());
        }

    }
    
}
