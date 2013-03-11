<?php


abstract class __TemplateFilter {
    
    const PRE_FILTER = 1;
    const POST_FILTER = 2;
    const OUTPUT_FILTER = 3;    
    
    protected $_type = self::POST_FILTER;
    
    abstract function executeFilter($compiled, __View &$view);
    
    public function getType() {
        return $this->_type;
    }
    
}
