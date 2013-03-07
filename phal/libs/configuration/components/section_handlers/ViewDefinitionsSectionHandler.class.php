<?php

/**
 * This is the section handler in charge of processing &lt;view-definitions&gt; configuration sections
 *
 */
class __ViewDefinitionsSectionHandler extends __CacheSectionHandler {
    
    public function &doProcess(__ConfigurationSection &$section) {
        $return_value = array( 'dynamic_rules' => array(), 
                               'static_rules'  => array() );
        $view_definition_sections = $section->getSections();
        foreach($view_definition_sections as $view_definition_section) {
            $view_definition = new __ViewDefinition();
            $view_definition->setViewCode($view_definition_section->getAttribute('code'));
            $view_definition->setViewClass($view_definition_section->getAttribute('class'));
            $view_definition_subsections = $view_definition_section->getSections();
            foreach($view_definition_subsections as $view_definition_subsection) {
                switch (strtoupper($view_definition_subsection->getName())) {
                    case 'PROPERTY':
                        $property_name  = $view_definition_subsection->getAttribute('name');
                        $property_value = $view_definition_subsection->getAttribute('value');
                        $view_definition->addProperty($property_name, $property_value);
                        break;
                }
            }            
            if(strpos($view_definition->getViewCode(), '*') !== false) {
                $clasify = 'dynamic_rules';
            }
            else {
                $clasify = 'static_rules';
            }
            $return_value[$clasify][strtoupper($view_definition->getViewCode())] =& $view_definition;
            unset($view_definition);
        }
        return $return_value;
    }
    
}