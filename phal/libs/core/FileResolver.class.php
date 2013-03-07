<?php

class __FileResolver {

    /**
     * Get an expression and returns a collection of files matching the expression
     * 
     * Expression may be in the form of:
     * <br>
     * path/.../*.xml  All xml files in whatever subdirectory under the specified path<br>
     * path/*.xml All xml files under the specified path (subdirectories not included)<br>
     * path/specificFile.xml An specific file under the specified path<br>
     *
     * @param string $expression
     * @return array Files matching the expression
     */
    static public function resolveFiles($expression) {
        $return_value = array();
        $filepattern = basename($expression);
        $ellipse_position = strpos($expression, '...');
        if($ellipse_position !== false) {
            $basedir = substr($expression, 0, $ellipse_position);
            $basedir = rtrim($basedir, DIRECTORY_SEPARATOR);
            $return_value = self::getFilesMatchingPattern($filepattern, $basedir, true);
        }
        else {
            $basedir = dirname($expression);
            $wildcard_position = strpos($expression, '*');
            if($wildcard_position !== false) {
                $return_value = self::getFilesMatchingPattern($filepattern, $basedir, false);
            }
            else {
                $return_value = array($expression);
            }
        }
        return $return_value;
    }
    
    /**
     * Get a pattern and a directory, and returns an array of filenames matching the given pattern
     *
     * @param string $file_pattern
     * @param string $current_dir
     * @param boolean $recursively
     * @return array An array of files matching the given pattern
     */
    static public function getFilesMatchingPattern($file_pattern, $current_dir, $recursively = true) {
        $return_value = array();
        if(is_readable($current_dir) && is_dir($current_dir)) {
            $dir = dir($current_dir);
            while (false !== ($file = $dir->read())) {
                $current_file = $current_dir . DIRECTORY_SEPARATOR . $file;
                $position_of_dot = strpos($file, ".");                
                if(is_readable($current_file) && $position_of_dot !== 0) {
                    if(is_file($current_file)) {
                        $file_matched = array();
                        if(preg_match('/^' . str_replace('*', '(.+?)', $file_pattern) . '$/', $file, $file_matched)) {
                            $return_value[strtoupper($current_file)] = $current_file;
                        }
                    }
                    else if(is_dir($current_file) && $recursively) {
                        $return_value += self::getFilesMatchingPattern($file_pattern, $current_file);
                    }
                }
            }
        }
        else {
            throw new Exception('The directory ' . $current_dir . ' specified in the includepath does not exists or is not readable.');
        }
        return $return_value;        
    }    
}
    
