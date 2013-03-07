<?php
 
class __ComponentLexer
{
    const OPEN_COMPONENT_TAG  = __ComponentParser::OPEN_COMPONENT_TAG; 
    const CLOSE_COMPONENT_TAG = __ComponentParser::CLOSE_COMPONENT_TAG;  
    const SHORT_COMPONENT_TAG = __ComponentParser::SHORT_COMPONENT_TAG;
    const ANYTHINGELSE        = __ComponentParser::ANYTHINGELSE;
    const OPEN_PROPERTY_TAG   = __ComponentParser::OPEN_PROPERTY_TAG;
    const CLOSE_PROPERTY_TAG  = __ComponentParser::CLOSE_PROPERTY_TAG;
    const RUNATSERVER_OPEN_TAG  = __ComponentParser::RUNATSERVER_OPEN_TAG;
    const RUNATSERVER_CLOSE_TAG = __ComponentParser::RUNATSERVER_CLOSE_TAG;
    const RUNATSERVER_SHORT_TAG = __ComponentParser::RUNATSERVER_SHORT_TAG;
 
    private $input;
    private $N;
    public $token;
    public $value;
    public $line;
    private $_string;
    private $debug = 0;
    
    function __construct($data)
    {
        $this->input = str_replace("\r\n", "\n", $data);
        $this->N = 0;
    }
 

    private $_yy_state = 1;
    private $_yy_stack = array();

    function yylex()
    {
        return $this->{'yylex' . $this->_yy_state}();
    }

    function yypushstate($state)
    {
        array_push($this->_yy_stack, $this->_yy_state);
        $this->_yy_state = $state;
    }

    function yypopstate()
    {
        $this->_yy_state = array_pop($this->_yy_stack);
    }

    function yybegin($state)
    {
        $this->_yy_state = $state;
    }



    function yylex1()
    {
        $tokenMap = array (
              1 => 7,
              9 => 7,
              17 => 0,
              18 => 3,
              22 => 0,
              23 => 3,
              27 => 0,
              28 => 0,
              29 => 0,
            );
        if ($this->N >= strlen($this->input)) {
            return false; // end of input
        }
        $yy_global_pattern = "/^(<[A-Za-z_][A-Za-z_0-9]*((\\s+[A-Za-z0-9_\-]+(\\s*=\\s*(?:\"[^\"]*\"|'[^']*'|[^'\"\/\s]+))?)+\\s*|\\s*)runat\\s*=\\s*\"(server|client)\"((\\s+[A-Za-z0-9_\-]+(\\s*=\\s*(?:\"[^\"]*\"|'[^']*'|[^'\"\/\s]+))?)+\\s*|\\s*)\/>)|^(<[A-Za-z_][A-Za-z_0-9]*((\\s+[A-Za-z0-9_\-]+(\\s*=\\s*(?:\"[^\"]*\"|'[^']*'|[^'\"\/>\s]+))?)+\\s*|\\s*)runat\\s*=\\s*\"(server|client)\"((\\s+[A-Za-z0-9_\-]+(\\s*=\\s*(?:\"[^\"]*\"|'[^']*'|[^'\"\/>\s]+))?)+\\s*|\\s*)>)|^(<\/[A-Za-z_][A-Za-z_0-9]*>)|^(<comp:[A-Za-z_][A-Za-z_0-9]*((\\s+[A-Za-z0-9_\-]+(\\s*=\\s*(?:\"[^\"]*\"|'[^']*'|[^'\"\/>\s]+))?)+\\s*|\\s*)>)|^(<\/comp:[A-Za-z_][A-Za-z_0-9]*>)|^(<comp:[A-Za-z_][A-Za-z_0-9]*((\\s+[A-Za-z0-9_\-]+(\\s*=\\s*(?:\"[^\"]*\"|'[^']*'|[^'\"\/\s]+))?)+\\s*|\\s*)\/>)|^(<comp-property\\s+name\\s*=\\s*\"[A-Za-z_][A-Za-z_0-9]*\"\\s*>)|^(<\/comp-property>)|^([\s\S])/";

        do {
            if (preg_match($yy_global_pattern, substr($this->input, $this->N), $yymatches)) {
                $yysubmatches = $yymatches;
                $yymatches = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                if (!count($yymatches)) {
                    throw new Exception('Error: lexing failed because a rule matched' .
                        'an empty string.  Input "' . substr($this->input,
                        $this->N, 5) . '... state YYINITIAL');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                if ($tokenMap[$this->token]) {
                    // extract sub-patterns for passing to lex function
                    $yysubmatches = array_slice($yysubmatches, $this->token + 1,
                        $tokenMap[$this->token]);
                } else {
                    $yysubmatches = array();
                }
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r1_' . $this->token}($yysubmatches);
                if ($r === null) {
                    $this->N += strlen($this->value);
                    $this->line += substr_count("\n", $this->value);
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->N += strlen($this->value);
                    $this->line += substr_count("\n", $this->value);
                    if ($this->N >= strlen($this->input)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                } else {                    $yy_yymore_patterns = array(
        1 => "^(<[A-Za-z_][A-Za-z_0-9]*((\\s+[A-Za-z0-9_\-]+(\\s*=\\s*(?:\"[^\"]*\"|'[^']*'|[^'\"\/>\s]+))?)+\\s*|\\s*)runat\\s*=\\s*\"(server|client)\"((\\s+[A-Za-z0-9_\-]+(\\s*=\\s*(?:\"[^\"]*\"|'[^']*'|[^'\"\/>\s]+))?)+\\s*|\\s*)>)|^(<\/[A-Za-z_][A-Za-z_0-9]*>)|^(<comp:[A-Za-z_][A-Za-z_0-9]*((\\s+[A-Za-z0-9_\-]+(\\s*=\\s*(?:\"[^\"]*\"|'[^']*'|[^'\"\/>\s]+))?)+\\s*|\\s*)>)|^(<\/comp:[A-Za-z_][A-Za-z_0-9]*>)|^(<comp:[A-Za-z_][A-Za-z_0-9]*((\\s+[A-Za-z0-9_\-]+(\\s*=\\s*(?:\"[^\"]*\"|'[^']*'|[^'\"\/\s]+))?)+\\s*|\\s*)\/>)|^(<comp-property\\s+name\\s*=\\s*\"[A-Za-z_][A-Za-z_0-9]*\"\\s*>)|^(<\/comp-property>)|^([\s\S])",
        9 => "^(<\/[A-Za-z_][A-Za-z_0-9]*>)|^(<comp:[A-Za-z_][A-Za-z_0-9]*((\\s+[A-Za-z0-9_\-]+(\\s*=\\s*(?:\"[^\"]*\"|'[^']*'|[^'\"\/>\s]+))?)+\\s*|\\s*)>)|^(<\/comp:[A-Za-z_][A-Za-z_0-9]*>)|^(<comp:[A-Za-z_][A-Za-z_0-9]*((\\s+[A-Za-z0-9_\-]+(\\s*=\\s*(?:\"[^\"]*\"|'[^']*'|[^'\"\/\s]+))?)+\\s*|\\s*)\/>)|^(<comp-property\\s+name\\s*=\\s*\"[A-Za-z_][A-Za-z_0-9]*\"\\s*>)|^(<\/comp-property>)|^([\s\S])",
        17 => "^(<comp:[A-Za-z_][A-Za-z_0-9]*((\\s+[A-Za-z0-9_\-]+(\\s*=\\s*(?:\"[^\"]*\"|'[^']*'|[^'\"\/>\s]+))?)+\\s*|\\s*)>)|^(<\/comp:[A-Za-z_][A-Za-z_0-9]*>)|^(<comp:[A-Za-z_][A-Za-z_0-9]*((\\s+[A-Za-z0-9_\-]+(\\s*=\\s*(?:\"[^\"]*\"|'[^']*'|[^'\"\/\s]+))?)+\\s*|\\s*)\/>)|^(<comp-property\\s+name\\s*=\\s*\"[A-Za-z_][A-Za-z_0-9]*\"\\s*>)|^(<\/comp-property>)|^([\s\S])",
        18 => "^(<\/comp:[A-Za-z_][A-Za-z_0-9]*>)|^(<comp:[A-Za-z_][A-Za-z_0-9]*((\\s+[A-Za-z0-9_\-]+(\\s*=\\s*(?:\"[^\"]*\"|'[^']*'|[^'\"\/\s]+))?)+\\s*|\\s*)\/>)|^(<comp-property\\s+name\\s*=\\s*\"[A-Za-z_][A-Za-z_0-9]*\"\\s*>)|^(<\/comp-property>)|^([\s\S])",
        22 => "^(<comp:[A-Za-z_][A-Za-z_0-9]*((\\s+[A-Za-z0-9_\-]+(\\s*=\\s*(?:\"[^\"]*\"|'[^']*'|[^'\"\/\s]+))?)+\\s*|\\s*)\/>)|^(<comp-property\\s+name\\s*=\\s*\"[A-Za-z_][A-Za-z_0-9]*\"\\s*>)|^(<\/comp-property>)|^([\s\S])",
        23 => "^(<comp-property\\s+name\\s*=\\s*\"[A-Za-z_][A-Za-z_0-9]*\"\\s*>)|^(<\/comp-property>)|^([\s\S])",
        27 => "^(<\/comp-property>)|^([\s\S])",
        28 => "^([\s\S])",
        29 => "",
    );

                    // yymore is needed
                    do {
                        if (!strlen($yy_yymore_patterns[$this->token])) {
                            throw new Exception('cannot do yymore for the last token');
                        }
                        if (preg_match($yy_yymore_patterns[$this->token],
                              substr($this->input, $this->N), $yymatches)) {
                            $yymatches = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                            next($yymatches); // skip global match
                            $this->token = key($yymatches); // token number
                            $this->value = current($yymatches); // token value
                            $this->line = substr_count("\n", $this->value);
                        }
                        $r = $this->{'yy_r1_' . $this->token}();
                    } while ($r !== null || !$r);
                    if ($r === true) {
                        // we have changed state
                        // process this token in the new state
                        return $this->yylex();
                    } else {
                        // accept
                        $this->N += strlen($this->value);
                        $this->line += substr_count("\n", $this->value);
                        return true;
                    }
                }
            } else {
                throw new Exception('Unexpected input at line' . $this->line .
                    ': ' . $this->input[$this->N]);
            }
            break;
        } while (true);

    } // end function


    const YYINITIAL = 1;
    function yy_r1_1($yy_subpatterns)
    {

    $this->token = self::RUNATSERVER_SHORT_TAG;
    }
    function yy_r1_9($yy_subpatterns)
    {

    $this->token = self::RUNATSERVER_OPEN_TAG;
    }
    function yy_r1_17($yy_subpatterns)
    {

    $this->token = self::RUNATSERVER_CLOSE_TAG;
    }
    function yy_r1_18($yy_subpatterns)
    {

    $this->token = self::OPEN_COMPONENT_TAG;
    }
    function yy_r1_22($yy_subpatterns)
    {

    $this->token = self::CLOSE_COMPONENT_TAG;
    }
    function yy_r1_23($yy_subpatterns)
    {

    $this->token = self::SHORT_COMPONENT_TAG;
    }
    function yy_r1_27($yy_subpatterns)
    {

    $this->token = self::OPEN_PROPERTY_TAG;
    }
    function yy_r1_28($yy_subpatterns)
    {

    $this->token = self::CLOSE_PROPERTY_TAG;
    }
    function yy_r1_29($yy_subpatterns)
    {

    $this->token = self::ANYTHINGELSE;
    }


    /**
     * return something useful, when a parse error occurs.
     *
     * used to build error messages if the parser fails, and needs to know the line number..
     *
     * @return   string 
     * @access   public
     */
    function parseError() 
    {
        return "Error at line {$this->yyline}";
        
    }
}