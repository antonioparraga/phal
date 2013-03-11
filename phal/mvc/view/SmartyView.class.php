<?php


class __SmartyView extends __TemplateEngineView {

    protected $_smarty;
	
    public function __construct() {
    	$this->_smarty = new Smarty();
        //Will also register filters executor methods:
    	$this->_smarty->registerFilter('pre', array(&$this, 'executePreFilters'));
    	$this->_smarty->registerFilter('post', array(&$this, 'executePostFilters'));
    	$this->_smarty->registerFilter('output', array(&$this, 'executeOutputFilters'));
    }
    
    public function getComponentParserClass() {
        return '__SmartyComponentParser';
    }
    
    public function setCaching($caching) {
        $caching = (bool) $caching;
        $this->_smarty->caching = $caching;
    }
    
    public function setCompileDir($compile_dir) {
    	$this->_smarty->compile_dir = $compile_dir;
    }

    public function assign($key, $value = null) {
    	$this->_smarty->assign($key, $value);
    }
        
    public function assignByRef($key, $value) {
    	$this->_smarty->assign_by_ref($key, $value);
    }
    
    public function isAssigned($key) {
    	$return_value = false;
    	$assignement = $this->_smarty->get_template_vars($key);
    	if($assignement !== null) {
    		$return_value = true;
    	}
    	return $return_value;
    }

    public function getAssignedVar($key) {
        $return_value = $this->_smarty->get_template_vars($key);
        return $return_value;        
    }
    
    protected function templatize($template_file) {
        $this->_smarty->assign('__view_code__', $this->getCode());
        return $this->_smarty->fetch($template_file);
    }

    protected function registerFunction($name, $value) {
    	$this->_smarty->register_function($name, $value);
    }
    
    protected function setCompileCheck($flag) {
    	$this->_smarty->compile_check = $flag;
    }
}
