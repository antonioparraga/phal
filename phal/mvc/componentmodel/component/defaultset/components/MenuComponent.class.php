<?php


class __MenuComponent extends __UIComponent {
    
    protected $_menu_resources_dir = null;
    
    protected $_configuration = null;
    
    protected $_I18n_support = true;
    
    public function setI18nSupport($I18n_support) {
        if(strtoupper($I18n_support) == 'NO') {
            $this->_I18n_support = false;
        }
        else {
            $this->_I18n_support = true;
        }
    }
    
    public function getI18nSupport() {
        return $this->_I18n_support;
    }
    
    public function setConfiguration($configuration) {
        $this->_configuration = $configuration;
    }
    
    public function getConfiguration() {
        return $this->_configuration;
    }
    
    public function setMenuResourcesDir($menu_resources_dir) {
        $this->_menu_resources_dir = $menu_resources_dir;
    }
    
    public function getMenuResourcesDir() {
        return $this->_menu_resources_dir;
    }

}
