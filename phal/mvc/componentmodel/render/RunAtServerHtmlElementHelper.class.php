<?php

class __RunAtServerHtmlElementHelper {

    static public function resolveComponentTag($html_tag_name, $attributes) {
        $return_value = null;
        switch($html_tag_name) {
            case 'a':
                $return_value = 'commandlink';
                break;
            case 'form':
                $return_value = 'form';
                break;
            case 'button':
                $return_value = 'commandbutton';
                break;
            default:
            	$return_value = $html_tag_name;
            	break;
        }
        return $return_value;        
    }
    
}
