<?php

/**
 * This utility class resolves configuration properties that represents path into
 * absolute paths (if applicable)
 *
 */
class __PathResolver {
    
    static public function resolvePath($path, $base_dir = null) {
        if($base_dir == null) {
            $current_context = __CurrentContext::getInstance();
            if($current_context != null) {
                $base_dir = $current_context->getBaseDir();
            }
            else {
                $base_dir = APP_DIR;
            }
        }
        if( preg_match( '/^\//', $path ) || preg_match('/^\w+:/', $path)) {
            $return_value = $path;
        }
        else {
            $return_value = rtrim($base_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
        }
        return $return_value;
    }
    
}