<?php

/**
 * Abstract class to define input type components (inputbox, textarea, ...)
 *
 */
abstract class __InputComponent extends __UIContainer implements __IPoolable, __IValueHolder {
    
	protected $_value = null;
	protected $_context_help = null;
	protected $_mask = null;
	protected $_prevent_xss = true;
	protected $_example = null;	
	
	public function setPreventXss($prevent_xss) {
	    $this->_prevent_xss = $this->_toBool($prevent_xss);
	}
	
	public function getPreventXss() {
	    return $this->_prevent_xss;
	}
	
	public function getValue() {
	    return $this->_value;
	}
	
	public function setValue($value) {
	    $this->_value = $this->_purifyValue($value);
	}
	
	public function setContextHelp($context_help) {
	    $this->_context_help = $context_help;
	}
	
	public function getContextHelp() {
	    return $this->_context_help;
	}
	
	public function setMask($mask) {
	    $this->_mask = $mask;
	}
	
	public function getMask() {
	    return $this->_mask;
	}
	
	public function setExample($example) {
	    $this->_example = $example;
	}
	
	public function getExample() {
	    return $this->_example;
	}
	
	public function reset() {
	    $this->setValue(null);
        $this->resetValidation();
	}
	
    protected function _purifyValue($val) {
        if($val == $this->_example) {
            $val = null;
        }
        else {
            static $purifier = null;
            if($this->_prevent_xss) {
                if (!empty($val)) {
                    if ($purifier == null && class_exists('HTMLPurifier')) {
                        if(iconv_get_encoding("internal_encoding") != "UTF-8") {
                            $config = HTMLPurifier_Config::createDefault();
                            $config->set('Core.Encoding', iconv_get_encoding("internal_encoding")); // replace with your encoding
                            $purifier = new HTMLPurifier($config);
                        }
                        else {
                            $purifier = new HTMLPurifier();
                        }
                    }
                    if($purifier != null) {
                        $val = $purifier->purify($val);
                    }
                }
            }
        }
        return $val;
    }
	
}
