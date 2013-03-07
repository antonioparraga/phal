<?php

class __CompositeComponentTemplateLocator extends __TemplateLocator {
    
    protected $_component_class = null;
    
    public function setComponentClass($component_class) {
        $this->_component_class = $component_class;
    }
    
    public function getSearchDirs() {
        $return_value = array();
        $class_file = __ClassLoader::getInstance()->getClassFile($component_class);
        if(!empty($class_file)) {
            $template_dir = dirname($class_file) . DIRECTORY_SEPARATOR . 'templates';
            $return_value[] = $template_dir;
        }
        $return_value = array_merge($return_value, parent::getSearchDirs());        
        return $return_value;
    }
    
}