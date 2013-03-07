<?php

/**
 * This class manages client-server UI component bindings
 * 
 * It contains all the {@link __UIBinding} instances created due to component rendering
 * 
 * @see __UIBinding
 *
 */
final class __UIBindingManager {
    
    static private $_instance = null;
    
    private $_ui_bindings = array();
    
    private $_current_request_ui_bindings = array();
    
    private function __construct() {
        $session = __CurrentContext::getInstance()->getSession();
        if($session->hasData('__UIBindingManager::_ui_bindings')) {
            $this->_ui_bindings =& $session->getData('__UIBindingManager::_ui_bindings');
        }
        else {
            $session->setData('__UIBindingManager::_ui_bindings', $this->_ui_bindings);
        }
    }
    
    /**
     * Gets a {@link __UIBindingManager} singleton instance
     *
     * @return __UIBindingManager
     */
    static public function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __UIBindingManager();
        }
        return self::$_instance;
    }
            
    /**
     * Binds a server end-point to a client end-point
     *
     * @param __IServerEndPoint $sep The server end-point
     * @param __IClientEndPoint $cep The client end-point
     */
    public function bind(__IServerEndPoint $sep, __IClientEndPoint $cep) {
        $ui_binding = new __UIBinding($sep, $cep);
        $this->registerUIBinding($ui_binding);
    }

    /**
     * Binds a server end-point to a client end-point (internally, it creates a {@link __UIBinding} instance), allowing the synchronization from client to server but not the oposite way
     * It also set the bound direction to {@link __IEndPoing::BIND_DIRECTION_C2S} to both end-points 
     *
     * @param __IServerEndPoint $sep
     * @param __IClientEndPoint $cep
     */
    public function bindFromClientToServer(__IServerEndPoint $sep, __IClientEndPoint $cep) {
        $cep->setBoundDirection(__IEndPoint::BIND_DIRECTION_C2S);
        $sep->setBoundDirection(__IEndPoint::BIND_DIRECTION_C2S);
        $ui_binding = new __UIBinding($sep, $cep);
        $this->registerUIBinding($ui_binding);
    }
    
    /**
     * Binds a server end-point to a client end-point (internally, it creates a {@link __UIBinding} instance), allowing the synchronization from server to client but not the oposite way
     * It also set the bound direction to {@link __IEndPoing::BIND_DIRECTION_S2C} to both end-points 
     *
     * @param __IServerEndPoint $sep
     * @param __IClientEndPoint $cep
     */
    public function bindFromServerToClient(__IServerEndPoint $sep, __IClientEndPoint $cep) {
        $cep->setBoundDirection(__IEndPoint::BIND_DIRECTION_S2C);
        $sep->setBoundDirection(__IEndPoint::BIND_DIRECTION_S2C);
        $ui_binding = new __UIBinding($sep, $cep);
        $this->registerUIBinding($ui_binding);
    }
    
    /**
     * Register a {@link __UIBinding} instance
     *
     * @param __UIBinding $ui_binding The __UIBinding to register to
     */
    public function registerUIBinding(__UIBinding $ui_binding) {
        $this->_ui_bindings[$ui_binding->getId()] = $ui_binding;
        $this->_current_request_ui_bindings[] = $ui_binding;
    }
    
    public function getCurrentRequestUIBindings() {
        return $this->_current_request_ui_bindings;
    }
    
    /**
     * Checks if a {@link __UIBinding} is already registered
     *
     * @param string $id The {@link __UIBinding} identifier
     * @return bool
     */
    public function hasUIBinding($id) {
        return key_exists($id, $this->_ui_bindings);
    }
    
    /**
     * Gets a {@link __UIBinding} corresponding to a given identifier
     *
     * @param string $id The identifier
     * @return __UIBinding
     */
    public function getUIBinding($id) {
        $return_value = null;
        if(key_exists($id, $this->_ui_bindings)) {
            $return_value = $this->_ui_bindings[$id];
        }
        return $return_value;
    }    
        
}
