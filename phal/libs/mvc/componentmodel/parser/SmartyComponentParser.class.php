<?php

class __SmartyComponentParser extends __ComponentParser {
    
    protected function _getStartRenderCode() {
        $return_value = '';
        if(count($this->_component_specs) > 0) {
            $component_render_class = __CurrentContext::getInstance()->getPropertyContent('DEFAULT_COMPONENT_RENDER_CLASS');
            $return_value .= "<?php\n";
            $return_value .= 'if ($_smarty_tpl->parent == null && !key_exists("__Phal_component_render_' . $this->_view_code . '__", $_smarty_tpl->parent->tpl_vars)) {' . "\n";
            $return_value .= '    $mark_as_root_template_' . $this->_view_code . " = true; \n";
            $return_value .= "}\n";
            $return_value .= '$component_render_' . $this->_view_code . ' = new ' . $component_render_class . '($_smarty_tpl->getVariable("__view_code__")->value);' . "\n";
            $return_value .= '$component_render_' . $this->_view_code . "->startRender();\n";
            $return_value .= '$_smarty_tpl->tpl_vars["__Phal_component_render_' . $this->_view_code . '__"] = new Smarty_variable(true);' . "\n";
            $return_value .= '$component_specs_' . $this->_view_code . ' = unserialize(base64_decode("' . base64_encode(serialize($this->_component_specs)) . '"));' . "\n";
            $return_value .= "?>\n";
        }
        return $return_value;
    }

    protected function _getComponentSingleTagCode(__ComponentSpec &$component_spec) {
        $return_value  = "<?php\n";
        $return_value .= '$component_render_' . $this->_view_code . '->markComponentSingleTag($component_specs_' . $this->_view_code . '["' . $component_spec->getId() . '"]);' . "\n";
        $return_value .= "?>";
        return $return_value;        
    }
    
    protected function _getComponentBeginTagCode(__ComponentSpec &$component_spec) {
        $return_value  = "<?php\n";
        $return_value .= '$component_render_' . $this->_view_code . '->markComponentBeginTag($component_specs_' . $this->_view_code . '["' . $component_spec->getId() . '"]);' . "\n";
        $return_value .= "?>";
        return $return_value;
    }    
    
    protected function _getRunAtServerHtmlElementCode(__ComponentSpec &$component_spec) {
        $return_value  = "<?php\n";
        $return_value .= '$component_render_' . $this->_view_code . '->markRunAtServerHtmlElement($component_specs_' . $this->_view_code . '["' . $component_spec->getId() . '"]);' . "\n";
        $return_value .= "?>";
        return $return_value;        
    }    

    protected function _getComponentEndTagCode(__ComponentSpec &$component_spec) {
        $return_value  = "<?php\n";
        $return_value .= '$component_render_' . $this->_view_code . '->markComponentEndTag($component_specs_' . $this->_view_code . '["' . $component_spec->getId() . '"]);' . "\n";
        $return_value .= "?>";
        return $return_value;
    }
    
    protected function _getComponentPropertyTagCode($property, $value) {
        $return_value  = "<?php\n";
        $return_value .= '$component_render_' . $this->_view_code . '->markPropertyBeginTag("' . $property . '");' . "\n";
        $return_value .= "?>" . $value . "<?php\n";
        $return_value .= '$component_render_' . $this->_view_code . '->markPropertyEndTag();' . "\n";
        $return_value .= "?>";
        return $return_value;
    }
    
    protected function _getEndRenderCode() {
        $return_value = '';
        if(count($this->_component_specs) > 0) {
            $return_value .= "<?php\n";
            $return_value .= '$component_render_' . $this->_view_code . "->endRender();\n";
            $return_value .= 'if (isset($mark_as_root_template_' . $this->_view_code . ")) {\n";
            $return_value .= '    $component_render_' . $this->_view_code . "->closeRender();\n";
            $return_value .= '    unset($_smarty_tpl->tpl_vars["__Phal_component_render_' . $this->_view_code . "__\"]);\n";
            $return_value .= '    unset($component_render_' . $this->_view_code . ");\n";
            $return_value .= "}\n";
            $return_value .= "?>\n";
        }
        return $return_value;
    }
    
}