%name REInverse
%include {

class __ExpressionVariable {

    private $_varname  = null;
    private $_varvalue = null;

    public function __construct($varname) {
        $this->_varname = $varname;
    }
    
    public function isOptional() {
        return false;
    }
    
    public function getVariableName() {
        return $this->_varname;
    }

    public function setVariableValue($value) {
        $this->_varvalue = $value;
    }

    public function getVariableValue() {
        return $this->_varvalue;
    }

    public function toString() {
        $return_value = $this->_varvalue;
        return $return_value;
    }

}

class __ExpressionTemplate {

    private $_variables   = array();
    private $_optional_variables = array();
    private $_expressions = array();
    private $_optional    = false;
    
    public function __clone() {
        foreach($this->_expressions as &$expression) {
            if(is_object($expression)) {
                $expression = clone $expression;
            }
        }
    }    
    
    public function __construct($expression = null) {
        if($expression != null) {
            if($expression instanceof __ExpressionTemplate) {
                $this->_variables   = $expression->_variables;
                $this->_optional_variables = $expression->_optional_variables;
                $this->_expressions = $expression->_expressions;
                $this->_optional    = $expression->_optional;
            }
            else {
                $this->addExpression($expression);
            }
        }
    }
    
    private function _analyzeExpression($expression) {
        if($expression instanceof __ExpressionTemplate) {
            $this->_variables += $expression->_variables;
            if($this->isOptional() && !$expression->isOptional()) {
                $this->_optional_variables += $expression->_variables;
            }
            else {
                $this->_optional_variables += $expression->_optional_variables;
            }
        }
        else if($expression instanceof __ExpressionVariable) {
            $var_name = $expression->getVariableName();
            if(!empty($var_name)) {
                $this->_variables[$expression->getVariableName()] = $expression->getVariableValue();
            }
            if($this->isOptional()) {
                $this->_optional_variables[$expression->getVariableName()] = $expression->getVariableValue();
            }
        }
        else if(is_array($expression)) {
            foreach($expression as $subexpression) {
                $this->_analyzeExpression($subexpression);
            }
        }
    }
    
    public function addExpression($expression) {
        if(!empty($expression)) {
            if($expression instanceof __ExpressionTemplate && !$expression->isOptional()) {
                $expressions = $expression->getExpressions();
                $this->_variables += $expression->_variables;
                if($this->isOptional()) {
                    $this->_optional_variables += $expression->_variables;
                }
                else {
                    $this->_optional_variables += $expression->_optional_variables;
                }
                foreach($expressions as $expression) {
                    $this->addExpression($expression);
                }
            }
            else {
                $this->_analyzeExpression($expression);
                if(is_string($expression) && count($this->_expressions) > 0 && is_string($this->_expressions[count($this->_expressions)-1])) {
                    $this->_expressions[count($this->_expressions)-1] .= $expression;
                }
                else {
                    $this->_expressions[] = $expression;
                    $this->_isString = false;
                }
            }
        }
    }

    public function isString() {
        return $this->_isString;        
    }
    
    public function addVariable($varname) {
        $this->_variables[$varname] = null;
        if($this->isOptional()) {
            $this->_optional_variables[$varname] = null;
        }
        $this->_isString = false;
    }

    public function setVariableValue($varname, $varvalue) {
        foreach($this->_expressions as &$expression) {
            if($expression instanceof __ExpressionTemplate && $expression->hasVariable($varname)) {
                $expression->setVariableValue($varname, $varvalue);
            }
            else if($expression instanceof __ExpressionVariable && $expression->getVariableName() == $varname) {
                $expression->setVariableValue($varvalue);
            }
        }
        $this->_variables[$varname] = $varvalue;
        if($this->isOptional()) {
            $this->_optional_variables[$varname] = $varvalue;
        }
    }

    public function isOptionalVariable($varname) {
        $return_value = false;
        if(key_exists($varname, $this->_optional_variables)) {
            $return_value = true;
        }
        return $return_value;
    }
    
    public function setVariableDefaultValue($varname, $varvalue) {
        foreach($this->_expressions as &$expression) {
            if($expression instanceof __ExpressionTemplate && $expression->hasVariable($varname)) {
                $expression->setVariableDefaultValue($varname, $varvalue);
            }
            else if($expression instanceof __ExpressionVariable && $expression->getVariableName() == $varname && $this->isOptional() == false) {
                $expression->setVariableValue($varvalue);
            }
        }
    }
    
    public function getVariables() {
        return $this->_variables;
    }

    public function getOptionalVariables() {
        return $this->_optional_variables;
    }
    
    public function getVariableValue($variable_name) {
        $return_value = null;
        if(key_exists($variable_name, $this->_variables)) {
            $return_value = $this->_variables[$variable_name];
        }
        return $return_value;
    }
    
    public function getExpressions() {
        return $this->_expressions;
    }
    
    public function hasVariable($varname) {
        return key_exists($varname, $this->_variables);
    }

    public function setOptional($optional) {
        $this->_optional = $optional;
        if($optional == true) {
            $this->_optional_variables = $this->_variables;
        }
    }

    public function isOptional() {
        return $this->_optional;
    }

    public function __toString() {
        $return_value = $this->getREInverse();
        if($return_value == null) {
            $return_value = "";
        }
        return $return_value;
    }

    public function toString() {
        return $this->getREInverse();
    }

    public function getREInverse() {
        $return_value = "";
        foreach($this->_expressions as $expression) {
            if(is_array($expression)) {
                $expression = $this->_selectSubExpression($expression);
            }
            if(!is_string($expression)) {
                if($expression->toString() == null) {
                    if($this->isOptional() && !$expression->isOptional()) {
                        return null;
                    }
                    else {
                        $expression = "";
                    }
                }
                else {
                    $expression = $expression->toString();
                }
            }
            $return_value .= $expression;
        }
        return $return_value;
    }

    private function _selectSubExpression($expression) {
        $return_value = null;
        $candidate_values = array();
        foreach($expression as $subexpression) {
            $candidate_value = (string) $subexpression;
            if($candidate_value != null) {
                $candidate_values[] = $candidate_value;
            }
        }
        //take the first candidate element always:
        if(count($candidate_values) > 0) {
            $return_value = end($candidate_values);
        }
        return $return_value;
    }
    
}



class __NamedGroup {

    private $_name = null;
    private $_pattern = null;

    public function __construct($name) {
        $this->_name = $name;
    }

    public function setName($name) {
        $this->_name = $name;
    }

    public function setPattern($pattern) {
        $this->_pattern = $pattern;
    }

    public function getName() {
        return $this->_name;
    }

    public function getPattern() {
        return $this->_pattern;
    }
    
}

}

%declare_class {class __REInverseParser}

%syntax_error {
    foreach ($this->yy_get_expected_tokens($yymajor) as $token) {
        $expect[] = self::$yyTokenName[$token];
    }
    throw new Exception('Error on regular expression: Unexpected ' . $this->tokenName($yymajor) . '(' . $TOKEN
        . '), expected one of: ' . implode(',', $expect));
}

%include_class {
/* ?><?php */

    public function __construct($lexer)
    {
        $this->_lexer = $lexer;
    }

    public function getResult() {
        return $this->_result;
    }

    private $_lexer;
    private $_namedgroups = array();
    private $_result = "";

    public $transTable =
        array(
                1  => self::ESCSEQ,
                2  => self::CARET,
                3  => self::DOLLAR,
                4  => self::DOT,
                5  => self::COMMA,
                6  => self::LBRACKET,
                7  => self::RBRACKET,
                8  => self::PIPE,
                9  => self::LPAR,
                10 => self::RPAR,
                11 => self::QUESTIONMARK,
                12 => self::ASTERISK,
                13 => self::PLUS,
                14 => self::LBRACE,
                15 => self::RBRACE,
                16 => self::SUB,
                17 => self::NONGROUPINGGRP,
                18 => self::NAMEDGROUPDEF,
                19 => self::NAMEDGROUP,
                20 => self::COMMENT,
                21 => self::MATCHIFNEXT,
                22 => self::MATCHIFNOTNEXT,
                23 => self::POSILOOKBEHIND,
                24 => self::NEGLOOKBEHIND,
                25 => self::INTEGER,
                26 => self::ANYCHAR,
                27 => self::URLVAR,
        );

    
}

%parse_accept {
    return $this->_result;
}

start          ::= re(A).                   { $this->_result = new __ExpressionTemplate(A); }

// ---------------- <re> ::= <union> 
re(A)          ::= union(B).                { 
    A = B;
}

// ---------------- <union> ::= <concat>"|"<union> | <concat>
union(A)       ::= concat(B) PIPE union(C). { 
    if(is_array(C)) {
        A = C;
    }
    else {
        A = array(C);
    }
    A[] = B;
}
union(A)       ::= concat(B).               { A = B;     } 

// ---------------- <concat> ::= <quant><concat> | <quant>
concat(A)      ::= quant(B) concat(C).      { 
    if(is_string(B) && is_string(C)) {
        A = B . C;
    }
    else {
        A = new __ExpressionTemplate();
        A->addExpression(B);
        A->addExpression(C);
    }
}
concat(A)      ::= quant(B).                { A = B;     }

// ---------------- <quant> ::= <group><quantifier><greedy> | <group>
quant(A)       ::= group(B) quantifier(C) greedy(D). {
    //By default, the expression is mandatory (optional means that the expression 
    //could be ignored if contained variables are not used when building the url.
    $optional = false;
    if(C == 0 && B instanceof __ExpressionTemplate && count(B->getVariables()) > 0) {
        C = 1;
        $optional = true;
        
    }
    if(C > 0) {
        if($optional || !is_string(B)) {
            A = new __ExpressionTemplate();
            A->setOptional(true);
            for($i = 0; $i < C; $i++) {
                A->addExpression(B);
            }
        }
        else {
            A = "";
            for($i = 0; $i < C; $i++) {
                A .= B;
            }
        }
    }
    else {
        A = "";
    }
}
quant(A)       ::= group(B).                { A = B;     }

// ---------------- <quantifier> ::= "*" | "?" | "+" | "{" <bound> "}"
quantifier(A)  ::= ASTERISK.                { A = 0;     }
quantifier(A)  ::= QUESTIONMARK.            { A = 0;     }
quantifier(A)  ::= PLUS.                    { A = 1;     }
quantifier(A)  ::= LBRACE bound(B) RBRACE.  { A = B;     }

// ---------------- <bound> ::= <num> | <num>"," | <num>","<num>
bound(A)       ::= INTEGER(B).                  { A = (int)B;    }
bound(A)       ::= INTEGER(B) COMMA.            { A = (int)B;    }
bound(A)       ::= INTEGER(B) COMMA INTEGER(C). { A = (int)B;    }

// ---------------- <greedy> ::= "?" | ""
greedy(A)     ::= QUESTIONMARK.
greedy(A)     ::= . 

// ---------------- <group> ::= "("<qmod><re>")" | <term> | "(" <namedgroup> ")"
group(A)       ::= URLVAR(B).               { 
    A = new __ExpressionTemplate(new __ExpressionVariable(B));
}
group(A)       ::= LPAR qmod(B) re(C) RPAR.     { 
    if (B == "comment") {
        A = null;
    }
    else {
        if( B instanceof __NamedGroup ) {
            B->setPattern(C);
            $this->_namedgroups[B->getName()] = B;
        }
        A = C;
    }
}
group(A)       ::= term(B).                     { A = B;    }
group(C)       ::= LPAR NAMEDGROUP(B) RPAR. {
    preg_match('/\?P\=([A-Za-z_][A-Za-z_0-9]*)/', B, $matched);
    $group_name = $matched[1];
    if(key_exists($group_name, $this->_namedgroups)) {
        $named_group = $this->_namedgroups[$group_name];
        C = $named_group->getPattern();
    }
    else {
        C = null;
    }
}
 
// ---------------- <qmod> ::= "" | "?#" | "?:" | "?=" | "?!" | "?<=" | "?<!"
//                           | "?P<id>"
//                           | <qexpr>
//                           | "?(" <num-or-expr> ")" <re-or-two>
//                           | "?"<modifs> | "?-"<modifs> | "?"<modifs>"-"<modifs>
qmod(A)        ::= NONGROUPINGGRP.
qmod(A)        ::= MATCHIFNEXT.
qmod(A)        ::= MATCHIFNOTNEXT.
qmod(A)        ::= COMMENT. { A = "comment"; }
qmod(C)        ::= NAMEDGROUPDEF(B). { 
    preg_match('/\?P\<([A-Za-z_][A-Za-z_0-9]*)\>/', B, $matched);
    $group_name = $matched[1];
    C = new __NamedGroup($group_name); 
}
qmod(A)        ::= POSILOOKBEHIND.
qmod(A)        ::= NEGLOOKBEHIND.
//qmod(A)        ::= qexpr(B).                    { A = B; }
//qmod(A)        ::= QUESTIONMARK LPAR numorexpr(B) RPAR reortwo(C).
//qmod(A)        ::= QUESTIONMARK modifs(B).
//qmod(A)        ::= QUESTIONMARK SUB modifs(B).
//qmod(A)        ::= QUESTIONMARK modifs(B) SUB modifs(C).
qmod(A)        ::= .


// ---------------- <term> ::= "." | "$" | "^" | <char> | <set>
term(A)        ::= DOT.                         { A = "a";  }
term(A)        ::= DOLLAR.                      { A = "";   }
term(A)        ::= CARET.                       { A = "";   } 
term(A)        ::= char(B).                     { A = B;    }
term(A)        ::= set(B).                      { A = B;    }

// ---------------- <char> ::= "\"<escaped> | anychar | integer
char(A)        ::= ESCSEQ escaped(B).           { A = B;    }
char(A)        ::= ANYCHAR(B).                  { A = B;    }
char(A)        ::= INTEGER(B).                  { A = B;    }

// ---------------- <escaped> ::= any character
escaped(A)     ::= ANYCHAR(B).                  {
    switch(B) {
        case 'B': //any middle char
             A = "a";
             break;
        case 'A': //begin of a word
             A = "a";
             break;
        case 'Z': //end of a word
             A = "a";
             break;
        case 'd': //any decimal digit
             A = "1";
             break;
        case 'D': //any non decimal digit
             A = "a";
             break;
        case 's': //any white character
             A = " ";
             break;
        case 'S': //any non white character
             A = "a";
             break;
        case 'w': //any word character
             A = "a";
             break;
        case 'W': //any non word character
             A = ",";
             break;
        case 't':
        case 'n':
        case 'r':
        case 'f':
        case 'a':
        case 'e':
        case 'l':
        case 'u':
        case 'L':
        case 'U':
        case 'E':
        case 'Q':
            A = "\\" . B;
            break;       
        default:
            A = B;
            break;
    }

}

escaped(A)     ::= ESCSEQ.       { A = "\\"; }
escaped(A)     ::= CARET.        { A = "^";  }
escaped(A)     ::= DOLLAR.       { A = "$";  }
escaped(A)     ::= DOT.          { A = ".";  }
escaped(A)     ::= COMMA.        { A = ",";  }
escaped(A)     ::= LBRACKET.     { A = "[";  }
escaped(A)     ::= RBRACKET.     { A = "]";  }
escaped(A)     ::= PIPE.         { A = "|";  }
escaped(A)     ::= LPAR.         { A = "(";  }
escaped(A)     ::= RPAR.         { A = ")";  }
escaped(A)     ::= QUESTIONMARK. { A = "?";  }
escaped(A)     ::= ASTERISK.     { A = "*";  }
escaped(A)     ::= PLUS.         { A = "+";  }
escaped(A)     ::= LBRACE.       { A = "{";  }
escaped(A)     ::= RBRACE.       { A = "}";  }
escaped(A)     ::= SUB.          { A = "-";  }



// ---------------- <special> ::= <backoctal>|<hexchar>|<controlchar>|<class>

// ---------------- <assert> ::= "b"|"B"|"A"|"z"|"Z"|"G"

// ---------------- <backoctal> ::= <digit> | <digit><digit> | "0"<oct><oct> | "+" | "&" | "`" | "'"

// ---------------- <hexchar> ::= "x"<hex><hex> | "x{"<hex><hex><hex><hex>"}"

// ---------------- <controlchar> ::= "c["

// ---------------- <namedchar> ::= "N{"<name>"}"

// ---------------- <class> ::= "p"<name>|"P"<name>|"[:"<posixclass>":]"|"[:^"<posixclass>":]"

// ---------------- <posixclass> ::= "alpha"|"alnum"|"ascii"|"cntrl"|"digit"|"graph"|"lower"|"print"|"punct"|"space"|"upper"|"word"|"xdigit"

// ---------------- <name> ::= <unicodeclass>

// ---------------- <unicodeclass> ::= "IsAlpha"|"IsAlnum"|"IsASCII"|"IsCntrl"|"IsDigit"|"IsGraph"|"IsLower"|"IsPrint"|"IsPunct"|"IsSpace"|"IsUpper"|"IsWord"|"IsXDigit"

// ---------------- <set> ::= "[" <set-items> "]" | "[^" <set-items> "]"
set(A)         ::= LBRACKET setitems(B) RBRACKET.       { A = B[0]; }
set(A)         ::= LBRACKET CARET setitems(B) RBRACKET. { 
    $curr_char = 33; //!
    $found = false;
    while($found == false) {
        if(in_array(chr($curr_char), B)) {
            $curr_char = $curr_char + 1;
        }
        else {
            $found = true;
        }
    }
    A = chr($curr_char);
}
// ---------------- <set-items> ::= <set-item> | <set-item> <set-items>
setitems(A)   ::= setitem(B). { A = B; }
setitems(A)   ::= setitem(B) setitems(C). { A = C + B; }

// ---------------- <set-item> ::= <range> | <char>
setitem(A)    ::= range(B). { A = B; }
setitem(A)    ::= char(B).  { A = array(B); }

// ---------------- <range> ::= <char> "-" <char>
range(A)       ::= char(B) SUB char(C). { 
    $min = ord(B);
    $max = ord(C);
    A = array();
    if($min > $max) {
        $tmp = $min;
        $min = $max;
        $max = $tmp;
    }
    for($i = $min; $i <= $max; $i++) {
        A[] = chr($i);
    }
}
// ---------------- <oct> ::= "0"|"1"|"2"|"3"|"4"|"5"|"6"|"7"
// ---------------- <digit> ::= "0"|"1"|"2"|"3"|"4"|"5"|"6"|"7"|"8"|"9"
// ---------------- <hex> ::= "0"|"1"|"2"|"3"|"4"|"5"|"6"|"7"|"8"|"9"|"a"|"b"|"c"|"d"|"e"|"f"|"A"|"B"|"C"|"D"|"E"|"F"
// ---------------- <mod> ::= "\i"|"\m"|"\s"|"\x"

