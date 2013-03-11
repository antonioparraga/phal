<?php

/**
 * This class represents a comment within a configuration file
 *
 */
class __ConfigurationComment extends __SimpleConfigurationComponent {
    
    protected $_single_line_comment_symbol = ';';
    
    public function setSingleLineCommentSymbol($single_line_comment_symbol) {
        $this->_single_line_comment_symbol = $single_line_comment_symbol;
    }
    
    public function getSingleLineCommentSymbol() {
        return $this->_single_line_comment_symbol;
    }
    
}