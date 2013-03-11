<?php

/**
 * This is the section handler in charge of processing &lt;filters&gt; configuration sections
 *
 */
class __FiltersSectionHandler extends __CacheSectionHandler {
    
    public function &doProcess(__ConfigurationSection &$section) {
        //initialize the return value with all the filter chains for all the available routes:
        $return_value = array();
        $filter_sections = $section->getSections();
        foreach($filter_sections as &$filter_section) {
            $filter_name = $filter_section->getAttribute('name');
            $filter_class = $filter_section->getAttribute('class');
            $filter = new $filter_class();
            if($filter_section->hasAttribute('order')) {
                $filter->setOrder($filter_section->getAttribute('order'));
            }
            if($filter_section->hasAttribute('execute-before-cache')) {
                $filter->setExecuteBeforeCache(__ConfigurationValueResolver::toBool($filter_section->getAttribute('execute-before-cache')));
            }
            if($filter instanceof __IFilter) {
                $routes_to_apply_to = $filter_section->getSection('apply-to');
                if($routes_to_apply_to != null) {
                    $routes_to_apply_to_sections = $routes_to_apply_to->getSections();
                    foreach($routes_to_apply_to_sections as $route_section) {
                        switch(strtoupper($route_section->getName())) {
                            case 'ALL-ROUTES':
                                if(!key_exists('*', $return_value)) {
                                    $return_value['*'] = array();
                                }
                                $return_value['*'][] =& $filter;
                                break;
                            case 'ROUTE':
                                $route_id = $route_section->getAttribute('id');
                                if(!key_exists($route_id, $return_value)) {
                                    $return_value[$route_id] = new __FilterChain();
                                }
                                $filter_chain =& $return_value[$route_id];
                                $filter_chain->addFilter($filter);
                                unset($filter_chain);
                                break;
                        }
                    }
                    unset($routes_to_apply_to_sections);
                }
                unset($routes_to_apply_to);
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('Unexpected class ' . get_class($filter) . '. A class implementing __IFilter was expected.');
            }
            unset($filter);
        }
        return $return_value;
    }    
    
}