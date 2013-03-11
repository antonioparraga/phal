<?php

class __ModelServiceArgument {

	protected $_name  = null;
	protected $_index = null;
	protected $_is_json = false;
	protected $_optional = false;

	public function setName($name) {
		$this->_name = $name;
	}

	public function getName() {
		return $this->_name;
	}

	public function setIndex($index) {
		$this->_index = $index;
	}

	public function getIndex() {
		return $this->_index;
	}

	public function setJson($json) {
		$this->_is_json = (bool)$json;
	}

	public function isJson() {
		return $this->_is_json;
	}

	public function setOptional($optional) {
		$this->_optional = (bool) $optional;
	}

	public function getOptional() {
		return $this->_optional;
	}

	public function isOptional() {
		return $this->_optional;
	}

}
