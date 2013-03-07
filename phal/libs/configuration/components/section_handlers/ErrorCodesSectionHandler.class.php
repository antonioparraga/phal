<?php

/**
 * This is the section handler in charge of processing &lt;error-codes&gt; configuration sections
 *
 */
class __ErrorCodesSectionHandler extends __CacheSectionHandler {
    
    public function &doProcess(__ConfigurationSection &$section) {
        $return_value = new __ErrorTable();
        $error_groups = $section->getSections();
        foreach($error_groups as &$error_group) {
            $this->_processErrorGroup($error_group, $return_value);
        }
        return $return_value;
    }

    private function _processErrorGroup(__ConfigurationSection &$error_group, __ErrorTable &$error_table) {
        $group_id = $error_group->getAttribute('id');
        $exception_class = $error_group->getAttribute('exception-class');
        $error_table->registerExceptionClass($group_id, $exception_class);
        $error_codes =& $error_group->getSections();
        foreach($error_codes as &$error_code) {
            $error_table->registerErrorCode($error_code->getAttribute('code'), $error_code->getAttribute('id'), $group_id);
        }
    }
            
    
    
}