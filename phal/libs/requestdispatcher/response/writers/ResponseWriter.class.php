<?php

class __ResponseWriter implements __IResponseWriter {
    
    const POSITION_TOP    = 1;
    const POSITION_BOTTOM = 2;
    const POSITION_EMBEDDED = 3;
    
    protected $_id = null;
    protected $_position = null;
    protected $_regexp = null;
    protected $_content  = null;
    protected $_response_writers = array();
    protected $_element_to_place_content_after = null;
    
    public function __construct($id) {
        if(empty($id)) {
            throw __ExceptionFactory::getInstance()->createException('A valid id is required to instantiate a __ResponseWriter object');
        }
        $this->_id = $id;
    }
    
    public function getId() {
        return $this->_id;
    }
    
    public function setContent($content) {
        $this->_content = $content;
    }
    
    public function setPosition($position) {
        $this->_position = $position;
    }
    
    public function getContent() {
        $return_value = $this->_content;
        $return_value .= $this->_getChildrensContent();        
        return $return_value;
    }
    
    protected function _getChildrensContent() {
        $return_value = '';
        foreach($this->_response_writers as $response_writer) {
            $return_value .= $response_writer->getContent();
        }
        return $return_value;
    }    
        
    public function __toString() {
        return $this->getContent();
    }
    
    public function write(__IResponse &$response) {
        switch($this->_position) {
            case self::POSITION_TOP:
                $response->dockContentOnTop($this->getContent(), $this->getId());
                break;
            case self::POSITION_BOTTOM:
                $response->dockContentAtBottom($this->getContent(), $this->getId());
                break;
            case self::POSITION_EMBEDDED:
                $response->placeContentAfterElement($this->getContent(), $this->getElementToPlaceContentAfter(), $this->getId());
                break;
            default:
                $response->addContent($this->getContent(), $this->getId());
                break;
        }
    }
    
    public function getElementToPlaceContentAfter() {
        return $this->_element_to_place_content_after;
    }
    
    public function setElementToPlaceContentAfter($element_to_place_content_after) {
        $this->_element_to_place_content_after = $element_to_place_content_after;
    }
    
    public function hasResponseWriter($id) {
        $return_value = false;
        if(key_exists($id, $this->_response_writers)) {
            $return_value = true;
        }
        else {
            foreach($this->_response_writers as &$response_writer) {
                if($response_writer->hasResponseWriter($id)) {
                    return true;
                }
            }
        }
        return $return_value;
    }
    
    public function &getResponseWriter($id) {
        $return_value = null;
        if(key_exists($id, $this->_response_writers)) {
            $return_value =& $this->_response_writers[$id];
        }
        else {
            foreach($this->_response_writers as &$response_writer) {
                $return_value = $response_writer->getResponseWriter($id);
                if($return_value != null) {
                    return $return_value;
                }
            }
        }
        return $return_value;
    }
    
    public function addResponseWriter(__IResponseWriter $response_writer) {
        $this->_response_writers[$response_writer->getId()] = $response_writer;
    }
    
    public function clear() {
        $this->_response_writers = array();
        $this->_content = null;
    }    

   
}