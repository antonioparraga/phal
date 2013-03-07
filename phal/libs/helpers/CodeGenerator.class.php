<?php

class __CodeGenerator {

    /**
     * This function validate a grantId for parameters and page request
     *
     * @param string $ The grant id to validate
     * @param string $ The page id for current grant id
     * @return boolean true if the grant id is correct for the specified page id
     */
    static function validateGrantId($grantId, $pageID)
    {
        // While not implemented:
        return false;
    }

    static function generateGrantId($pageID, $formID)
    {
        return "";
    }    
    
    /**
     * This function generate a submitCode according to a pageID and formID
     *
     * @param string $ The id of the page
     * @param string $ The id of the form
     * @return string The requested submitCode
     */
    static function getSubmitCode($action_code, $form_id = null)
    {
        $submit_code = $action_code;
        if($form_id != null) {
            $submit_code .= "~" . $form_id;
        }
        $submit_code = md5($submit_code);
        return $submit_code;
    }
    
    static function getUnikeCode() {
        $return_value = md5(uniqid(rand(), true));
        return $return_value;
    }
    
    /**
     * This function generate a submitCode according to a pageID and formID
     *
     * @param string $ The id of the page
     * @param string $ The id of the form
     * @return string The requested submitCode
     */
    static function generateSubmitCode($pageID, $formID)
    {
        return self::GetSubmitCode($pageID, $formID);
    }    
    
}

?>