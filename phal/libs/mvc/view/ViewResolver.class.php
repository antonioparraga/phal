<?php

/**
 * This is the class in charge of resolve what's the __View that correspondes with each {@link __ModelAndView} instance.
 *
 */
class __ViewResolver  {

    private static $_instance = null;

    private $_dirty = false;

    private $_view_definitions = array();
    
    private $_views = array();

    private function __construct() {
        $view_definitions = __ContextManager::getInstance()->getCurrentContext()->getConfiguration()->getSection('configuration')->getSection('view-definitions');
        if(is_array($view_definitions)) {
            $this->_view_definitions =& $view_definitions;
        }
    }

    public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __ViewResolver();
        }
        return self::$_instance;
    }

    /**
     * This method returns an {@link __View} child instance that correspond with a specified view code.
     *
     * @param string $view_code An view code
     * @return __View An {@link __View} child instance that correspond with the specified view code
     * 
     * @throws __SecurityException If no view is found for the specified view code, a __SecurityException will be raised.
     * 
     */
    public function &getView($view_code)
    {
        $return_value = $this->_createView($view_code);
        if(!$return_value instanceof __IView) {
            throw __ExceptionFactory::getInstance()->createException('ERR_CAN_NOT_RESOLVE_VIEW', array($view_code_search_key));
        }
        return $return_value;
    }
    
    /**
	 * This is a factory method for creating instances implementing the {@link __IView}
	 *
	 * @param string $view_code The view code
	 * @return __IView an instance of a class implementing the {@link __IView}
	 */
    private function _createView($view_code) {
        $return_value = null;
        $path = null;
        if(!empty($view_code)) {
            $view_code_search_key = strtoupper(trim($view_code));            
            //check static rules:
            if(key_exists($view_code_search_key, $this->_view_definitions['static_rules'])) {
                try {
                    $view_definition = $this->_view_definitions['static_rules'][$view_code_search_key];
                    $return_value    = $view_definition->getView();
                }
                catch (Exception $e) {
                    throw __ExceptionFactory::getInstance()->createException('ERR_VIEW_NON_LOADABLE', array($view_code_search_key, $e->getMessage()));
                }
            }
            //check dynamic rules:
            else {
                foreach($this->_view_definitions['dynamic_rules'] as $view_definition) {
                    if( $return_value == null && $view_definition->isValidForViewCode($view_code_search_key)) {
                        $return_value = $view_definition->getView($view_code);
                    }
                }
            }
        }
        if($return_value == null) {
            throw __ExceptionFactory::getInstance()->createException('ERR_CAN_NOT_RESOLVE_VIEW', array($view_code_search_key));
        }
        else if(!$return_value instanceof __IView) {
            throw __ExceptionFactory::getInstance()->createException('ERR_WRONG_VIEW_CLASS', array(get_class($return_value)));
        }
        else {
            $return_value->setCode($view_code);
        }
        return $return_value;
    }


}