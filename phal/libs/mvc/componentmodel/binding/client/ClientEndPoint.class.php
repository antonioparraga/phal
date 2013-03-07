<?php

/**
 * Abstract client endpoint implementation
 *
 */
abstract class __ClientEndPoint implements __IClientEndPoint {
    
    protected $_ui_binding = null;
    protected $_bound_direction = __IEndPoint::BIND_DIRECTION_ALL;
    protected $_synchronization_prefilter_callback = null;
    protected $_synchronized = false;
    protected $_value = null;
    
    public function setSynchronizationPrefilterCallback(__Callback $callback) {
        $this->_synchronization_prefilter_callback = $callback;
    }
    
    public function getSynchronizationPrefilterCallback() {
        return $this->_synchronization_prefilter_callback;
    }
    
    public function setUiBinding(__UIBinding &$ui_binding) {
        $this->_ui_binding =& $ui_binding;
    }

    public function &getUiBinding() {
        return $this->_ui_binding;
    }    
    
    public function getBoundDirection() {
        return $this->_bound_direction;
    }
    
    public function setBoundDirection($bound_direction) {
        $this->_bound_direction = $bound_direction;
    }    

    public function setValue($value) {
        $this->_value = $value;
    }
    
    public function getValue() {
        return $this->_value;
    }
    
    public function synchronize(__IServerEndPoint &$server_end_point) {
        //if the server end-point is a valueholder, will synchronize
        //only if server and client values are different:
        if( $server_end_point instanceof __IValueHolder ) {
            $value = $server_end_point->getValue();
            $current_value = $this->getValue();
            //if current value is different to new value
            //note that we are avoiding the case when comparing a null value with an empty string it
            //depends on how client submit values to server for empty fields
            if($current_value !== $value && 
             !(empty($current_value) && empty($value))) {
                $this->_value = $value;
                $this->setAsUnsynchronized();
            }
        }
        else {
            //if the server end-point is not a valueholder
            $this->setAsUnsynchronized();
        }
    }

    /**
     * Check if server and client are unsynchronized
     *
     * @return bool
     */
    public function isUnsynchronized() {
        if($this->_synchronization_prefilter_callback != null) {
            $return_value = $this->_synchronization_prefilter_callback->execute();
            if($return_value !== false) {
                $return_value = !$this->_synchronized;
            }
        }
        else {
            $return_value = !$this->_synchronized;
        }
        return $return_value;
    }    
    
    /**
     * Set the client end-point as synchronized
     *
     */
    public function setAsSynchronized() {
        $this->_synchronized = true;
    }
    
    /**
     * Set the client end-point as unsynchronized
     *
     */
    public function setAsUnsynchronized() {
        $this->_synchronized = false;
    }
    
}