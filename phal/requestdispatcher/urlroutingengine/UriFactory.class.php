<?php


/**
 * This class is the __Uri factory class, a class in charge of __Uri instances creation.
 *
 * @see __Uri
 * 
 */
class __UriFactory {

    private static $_instance = null;
    
    private function __construct() {
    }
    
    /**
     * Singleton method to retrieve the __UriFactory instance
     *
     * @return __UriFactory The singleton __UriFactory instance
     */
    public static function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __UriFactory();
        }
        return self::$_instance;
    }
    
    /**
     * Factory method to create {@link __Uri} instances
     *
     * @param string $url The url to use to create the uri
     * @return __Uri The requested {@link __Uri} instance
     */
    public function &createUri($url = null) {
        $return_value = new __Uri($url);
        return $return_value;
    }

}