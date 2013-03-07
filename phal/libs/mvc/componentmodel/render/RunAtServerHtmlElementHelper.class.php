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
            case 'textarea':
                $return_value = 'textarea';
                break;
            case 'select':
                $return_value = 'combobox';
                break;
            case 'button':
                $return_value = 'commandbutton';
                break;
            case 'div':
            case 'span':
            	$return_value = 'area';
                break;
            case 'input':
                if(key_exists('type', $attributes)) {
                    switch($attributes['type']) {
                        case 'text':
                            $return_value = 'inputbox';
                            break;
                        case 'checkbox':
                            $return_value = 'checkbox';
                            break;
                        case 'password':
                            $return_value = 'inputbox';
                            break;
                        case 'hidden':
                            $return_value = 'hidden';
                            break;
                        case 'submit':
                            $return_value = 'commandbutton';
                            break;
                        case 'button':
                            $return_value = 'commandbutton';
                            break;
                    }
                }
                break;
            default:
            	$return_value = $html_tag_name;
            	break;
        }
        return $return_value;        
    }
    
}
