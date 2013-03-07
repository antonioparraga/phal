%name Component
%include {

}

%declare_class {abstract class __ComponentParser}

%syntax_error {
    $expect = array();
    foreach ($this->yy_get_expected_tokens($yymajor) as $token) {
        $expect[] = $this->_getTokenName($token);
    }
    throw new __UIComponentException('Error parsing the template for view ' . $this->_view_code . ': Unexpected ' . $this->_getTokenName($yymajor) . '(' . $TOKEN
        . '), expected one of: ' . implode(',', $expect));
}

%include_class {
/* ?><?php */

    protected $_component_specs = array();
    protected $_component_specs_stack = null;
    protected $_properties_stack = array();
    protected $_result;
    protected $_view_code = null;
    
    public $transTable =
        array(
                1  => self::OPEN_COMPONENT_TAG,
                2  => self::CLOSE_COMPONENT_TAG,
                3  => self::SHORT_COMPONENT_TAG,
                4  => self::ANYTHINGELSE,
                5  => self::OPEN_PROPERTY_TAG,
                6  => self::CLOSE_PROPERTY_TAG,
                7  => self::RUNATSERVER_SHORT_TAG,
                8  => self::RUNATSERVER_OPEN_TAG,
                9  => self::RUNATSERVER_CLOSE_TAG,
                
        );

    protected $_token_names =
        array(
                self::OPEN_COMPONENT_TAG  => 'Component open tag',
                self::CLOSE_COMPONENT_TAG => 'Component close tag (</comp:[component]>)',
                self::SHORT_COMPONENT_TAG => 'Component tag',
                self::ANYTHINGELSE        => 'Any character',
                self::OPEN_PROPERTY_TAG   => 'Property open tag',
                self::CLOSE_PROPERTY_TAG  => 'Property close tag within the [component] component',
                self::RUNATSERVER_SHORT_TAG => 'Html element (run at server)',
                self::RUNATSERVER_OPEN_TAG  => 'Html element (run at server) open tag',
                self::RUNATSERVER_CLOSE_TAG => 'Html element (run at server) close tag',
        );

    private function _getTokenName($token_id) {
        $return_value = $token_id;
        if(key_exists($token_id, $this->_token_names)) {
            $return_value = $this->_token_names[$token_id];
            $current_component_spec = $this->_getCurrentComponentSpec();
            if($current_component_spec != null) {
                $return_value = str_replace('[component]', $current_component_spec->getTag(), $return_value);
            }
        }
        return $return_value;
    }        

    public function __construct(__View &$view)
    {
        $this->_view_code = $view->getCode();
        $this->_component_specs_stack  = new __Stack();
        $this->_properties_stack = new __Stack();
    }
    
    public function getResult() {
        return $this->_result;
    }   
    
    protected function _getTagName($tag_expression) {
    	$return_value = null;
        if(preg_match('/\<\/?comp\:([A-Za-z_][A-Za-z_0-9]*)/i', $tag_expression, $matched)) {
        	$return_value = $matched[1];
        }
        return $return_value;
    }

    protected function _getHtmlTagName($tag_expression) {
    	$return_value = null;
        if(preg_match('/\<\/?([A-Za-z_][A-Za-z_0-9]*)/i', $tag_expression, $matched)) {
        	$return_value = $matched[1];
        }
        return $return_value;
    }

    //todo: improve this method!
    protected function _getAttributeList($tag_expression) {
        $return_value = null;
        if(preg_match('/<\/?[A-Za-z_][A-Za-z_0-9\:]*((\s+\w+(\s*=\s*(?:\"[^\"]*\"|\'[^\']*\'|[^\'\">\s]+))?)+\s*|\s*)\/?>/i', $tag_expression, $matched)) {
            $params_str = trim($matched[1]);
            $return_value = $this->_splitParams($params_str);
        }
        return $return_value;
    }
    
    protected function _removeFromAttributeList($tag_expression, $attribute_name) {
        $return_value = preg_replace('/' . $attribute_name . '\s*\=\s*[\"\'][^\"\']+[\"\']/i', '', $tag_expression);
        return $return_value;
    
    }
    
    protected function _getComponentPropertyName($tag_expression) {
    	$return_value = null;
	    if(preg_match('/\<comp\-property\s+name\s*\=\s*[\"\']([A-Za-z_][A-Za-z_0-9]*)[\"\']\s*>/i', $tag_expression, $matched)) {
	        $return_value = $matched[1];
	    }
	    return $return_value;
    }

    public function _getViewCode() {
        return $this->_view_code;
    }

	/**
	 * This rather nasty looking function takes the parameters
	 * inside our opening tag and puts them into an associative array.
	 * 
	 * @param string $params_str The string to split into parameters
	 * @return array An associative array of pairs [key, value]
	 */ 
	protected function _splitParams($params_str) {
	    $params_str = trim($params_str);
	    $length = strlen($params_str);
	    $return_value = array();
	    $key = '';
	    $val = '';
	    $single_quote_hex = "\x27";
	    $double_quote_hex = "\x22";
	    $done = false;
	    $remove_spaces = false;
	    /* $mode:
	      0 - search for key
	      1 - search for =
	      2 - search for end
	      3 - search for end with quotes */
	    $mode = 0;
		$quote_character = null;
	    for ($x = 0; $x < $length; $x++) {
	        $chr = substr($params_str, $x, 1);
	        if(preg_match('/\s/', $chr)) {
	        	$is_space = true;
	        }
	        else {
	        	$is_space = false;
	        }
			if ($remove_spaces == false || !$is_space) {
				$remove_spaces = false; //in any case
		        switch ($mode) {
		        	//initial state:
		            case 0:
		                if ( !$is_space ) {
		                	$key .= $chr;
		                }
		                $mode = 1;
		                break;
		            //scan the key:
		            case 1:
		            	if ($chr != '=') {
		            		if($is_space) {
		            			$remove_spaces = true;	
		            		}
		            		else {
		                		$key .= $chr;
		            		}
		                }
		                else {
		                	$mode = 2;
		                	$remove_spaces = true;
		                }
		            	break;
		            //scan the value:
		            case 2:
		                if (($chr == "\"" || $chr == "'") && ($val == '')) {
		                    $quote_character = $chr;
		                    $mode = 3;
		                } else {
		                    if ( $is_space || ($x == ($length - 1)) ) {
		                        $done = true;
		                        if (! $is_space ) {
		                            $val .= $chr;
		                        }
		                    } else {
		                        $val .= $chr;
		                    }
		                }
		                break;
		            case 3:
		                if (($chr == $quote_character)||($x == ($length - 1))) {
		                    $done = true;
		                    if ($chr != $quote_character) {
		                        $val .= $chr;
		                    }
		                } else {
		                    $val .= $chr;
		                }
		            break;
		        }
			}
			
	        if ($done == true) {
	            $mode = 0;
	            $done = false;
                //remove delimiter quotes if exists: 
                $key = trim(strtolower($key));
                switch($quote_character) {
                    case '"':
                        $val = trim($val, $double_quote_hex);
                        break;
                    case "'":
                        $val = trim($val, $single_quote_hex);
                        break;
                }
	            $return_value[$key] = $val;
	            $key = '';
	            $val = '';
	        }
	    }
	    return $return_value;
	}
    
    
    protected function _pushComponentSpec(__ComponentSpec &$component_spec) {
        return $this->_component_specs_stack->push($component_spec);
    }
    
    protected function &_popComponentSpec() {
        $return_value = $this->_component_specs_stack->pop();
        return $return_value;
    }    

    protected function &_peekComponentSpec() {
        $return_value = $this->_component_specs_stack->peek();
        return $return_value;
    }    
    
    protected function _registerComponentSpec(__ComponentSpec &$component_spec) {
        if($this->_properties_stack->count() == 0 && $this->_component_specs_stack->count() > 0) {
            $current_component_spec  = $this->_component_specs_stack->peek();
            $current_component_class = $current_component_spec->getClass();
            if(!is_subclass_of($current_component_class, '__IContainer')) {
                throw __ExceptionFactory::getInstance()->createException('ERR_UI_COMPONENT_IS_NOT_CONTAINER', array($current_component_spec->getTag(), $component_spec->getTag()));
            }
        }
        $this->_component_specs[$component_spec->getId()] =& $component_spec;            
    }

    protected function &_getCurrentComponentSpec() {
        $return_value = null;
        if($this->_component_specs_stack->count() > 0) {
            $return_value = $this->_component_specs_stack->peek();
        }
        return $return_value;
    }    
    
    protected function _pushProperty($property) {
        $this->_properties_stack->push($property);
    }

    protected function _popProperty() {
        return $this->_properties_stack->pop();
    }
    
    protected function _getCurrentProperty() {
        $return_value = null;
        if($this->_properties_stack->count() > 0) {
            $return_value = $this->_properties_stack->peek();
        }
        return $return_value;
    }
    
    abstract protected function _getStartRenderCode();

    abstract protected function _getComponentSingleTagCode(__ComponentSpec &$component_spec);

    abstract protected function _getRunAtServerHtmlElementCode(__ComponentSpec &$component_spec);
    
    abstract protected function _getComponentBeginTagCode(__ComponentSpec &$component_spec);

    abstract protected function _getComponentEndTagCode(__ComponentSpec &$component_spec);
    
    abstract protected function _getComponentPropertyTagCode($property, $value);
    
    abstract protected function _getEndRenderCode();
    
}

%parse_accept {
    return $this->_result;
}

start         ::= ui_code(A) . 
{ 
    $this->_result = $this->_getStartRenderCode() . A . $this->_getEndRenderCode();
}

ui_code(A)    ::= ui_code(B) anychar(C) . 
{
    A = B . C;
}

ui_code(A)    ::= ui_code(B) component_tag(C) . 
{
    A = B . C;
}

ui_code(A)    ::= ui_code(B) component_property(C) . 
{
    A = B . C;
}

ui_code(A)    ::= . 
{
    A = '';
}

component_tag(A) ::= r_open_component_tag(B) component_body(C) r_close_component_tag(D) . 
{
    A = B . C . D;
}

component_tag(A) ::= RUNATSERVER_SHORT_TAG(B) .
{ 
    //Setup, validate and register current component:
    $tag_name  = $this->_getHtmlTagName(B);
    $attribute_list = $this->_getAttributeList(B);
    if(key_exists('componenttag', $attribute_list)) {
        $component_tag = $attribute_list['component'];
    }
    else {
        $component_tag = __RunAtServerHtmlElementHelper::resolveComponentTag($tag_name, $attribute_list);
    }    
    $component_spec = __ComponentSpecFactory::getInstance()->createComponentSpec($component_tag);
    $component_spec->setDefaultValues($attribute_list);
    $component_spec->setViewCode($this->_view_code);
    $this->_registerComponentSpec($component_spec);  
    if($this->_getCurrentProperty() == null) {
        A = $this->_getComponentSingleTagCode($component_spec);
    }
    else {
        A = $component;
    }
}

component_tag(A) ::= RUNATSERVER_OPEN_TAG(B) .
{
   //Setup, validate and register current component:
    $tag_name  = $this->_getHtmlTagName(B);
    $attribute_list = $this->_getAttributeList(B);
    if(key_exists('componenttag', $attribute_list)) {
        $component_tag = $attribute_list['component'];
    }
    else {
        $component_tag = __RunAtServerHtmlElementHelper::resolveComponentTag($tag_name, $attribute_list);
    }    
    $component_spec = __ComponentSpecFactory::getInstance()->createComponentSpec($component_tag);
    $component_spec->setDefaultValues($attribute_list);
    $component_spec->setViewCode($this->_view_code);
    if(is_array($attribute_list) && key_exists('runat', $attribute_list) && strtoupper($attribute_list['runat']) == 'SERVER') {
    	$component_spec->setRunAtServer(true);
    }
    else {
        $component_spec->setRunAtServer(false);
    }
    $this->_registerComponentSpec($component_spec);
    $this->_pushComponentSpec($component_spec);
    A = $this->_getComponentBeginTagCode($component_spec);
}

component_tag(A) ::= RUNATSERVER_CLOSE_TAG(B) .
{
    //Retrieve the current component and perform validations:
    $tag_name = $this->_getHtmlTagName(B);
    $component_spec =& $this->_peekComponentSpec();
    if($component_spec != null && $component_spec->getTag() == $tag_name && $component_spec->getRunAtServer() == true) {
    	$this->_popComponentSpec();
        if($this->_getCurrentProperty() == null) {
            A = $this->_getComponentEndTagCode($component_spec);
        }
        else {
            A = $component;
        }
    }
    else {
        A = B;
    }
}

component_tag(A) ::= SHORT_COMPONENT_TAG(B) .
{ 
    //Setup, validate and register current component:
    $tag_name  = $this->_getTagName(B);
    $attribute_list = $this->_getAttributeList(B);
    $component_spec = __ComponentSpecFactory::getInstance()->createComponentSpec($tag_name);
    $component_spec->setDefaultValues($attribute_list);
    $component_spec->setViewCode($this->_view_code);
    $this->_registerComponentSpec($component_spec);  
    if($this->_getCurrentProperty() == null) {
        A = $this->_getComponentSingleTagCode($component_spec);
    }
    else {
        A = $component;
    }
}

r_open_component_tag(A) ::= OPEN_COMPONENT_TAG(B) .
{
    //Setup and validate current component:
    $tag_name  = $this->_getTagName(B);
    $component_spec = __ComponentSpecFactory::getInstance()->createComponentSpec($tag_name);
    $attribute_list = $this->_getAttributeList(B);
    $component_spec->setDefaultValues($attribute_list);
    $component_spec->setViewCode($this->_view_code);
    $this->_registerComponentSpec($component_spec);
    $this->_pushComponentSpec($component_spec);
    A = $this->_getComponentBeginTagCode($component_spec);
}

r_close_component_tag(A) ::= CLOSE_COMPONENT_TAG(B) .
{
    //Retrieve the current component and perform validations:
    $tag_name = $this->_getTagName(B);
    $component_spec =& $this->_popComponentSpec();
    if(strtoupper($component_spec->getTag()) != strtoupper($tag_name)) {
        throw __ExceptionFactory::getInstance()->createException('ERR_UI_UNEXPECTED_CLOSE_TAG', array($tag_name, $component_spec->getTag()));
    }
    if($this->_getCurrentProperty() == null) {
        A = $this->_getComponentEndTagCode($component_spec);
    }
    else {
        A = $component;
    }
}

component_body(A) ::= ui_code(B) .
{
    A = B;
}

//property_value can be:
// - another component: the component itself will be set to the parent component's property
// - literals (and components): all will be transformed to string and set to the paren component's property
component_property(A)   ::= r_open_property_tag(B) property_value(C) r_close_property_tag(D) . 
{
    if($this->_getCurrentComponentSpec() == null) {
        throw __ExceptionFactory::getInstance()->createException('ERR_UI_UNEXPECTED_PROPERTY_TAG');
    }
    else {
        $property_name  = B;
        //Now will parse the property value:
        $value_is_string = false;
        $property_value = null;
        $property_value_as_string = '';
        $property_value_array = C;
        foreach($property_value_array as $property_value_part) {
            if(is_string($property_value_part)) {
                $property_value_as_string .= $property_value_part;
                if(trim($property_value_part) != '') {
                    $value_is_string = true;
                }
            }
            else if($property_value_part instanceof __IComponent) {
                $property_value_as_string .= $property_value_part->__toString();
                if($component_as_value == null) {
                    $property_value = $property_value_part;
                }
                else {
                    $value_is_string = true;
                }
            }
        }
        if($value_is_string) {
            $property_value = $property_value_as_string;
        }
        $component_spec = $this->_getCurrentComponentSpec();
        A = $this->_getComponentPropertyTagCode($property_name, $property_value);
    }
}

r_open_property_tag(A) ::= OPEN_PROPERTY_TAG(B) .
{
	$property_name = $this->_getComponentPropertyName(B);
    $this->_pushProperty($property_name);
    A = $property_name;
}

r_close_property_tag(A) ::= CLOSE_PROPERTY_TAG(B) .
{
    $this->_popProperty();
    A = '';
}

property_value(A) ::= property_value(B) component_tag(C) property_value(D) .
{
    A = B;
    A[] = C;
    foreach(D as $item_from_D) {
        A[] = $item_from_D;
    }
}

property_value(A) ::= literal(B) .
{
	A = array(B);
}

literal(A)    ::= literal(B) ANYTHINGELSE(C) . 
{
    A = B . C;
}

literal(A)    ::= . 
{ 
    A = '';
}

anychar(A) ::= ANYTHINGELSE(B) .
{
    A = B;
}
