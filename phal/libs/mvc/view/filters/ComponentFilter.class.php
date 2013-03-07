<?php


class __ComponentFilter extends __TemplateFilter {
       
    protected $_type = self::POST_FILTER;
	
    /**
     * This method executes a filter that detect special tag <component:xxx> and generates code according to the component type
     *
     * @param string Already compiled code by the template engine
     * @param __View The current __View derived instance
     * @return string The compiled code with the filter applied
     */
    public function executeFilter($compiled, __View &$view)
    {
        $return_value = $compiled; //by default will return the $compiled content without changes
        if( $view instanceof __TemplateEngineView ) {
            $component_parser_class = $view->getComponentParserClass();
            $lex    = new __ComponentLexer($compiled);
            $parser = new $component_parser_class($view);
            while ($lex->yylex()) {
                $parser->doParse($lex->token, $lex->value);
            }
            $parser->doParse(0, 0);
            $return_value = $parser->getResult();
        }
        return $return_value;
    }
    
}
