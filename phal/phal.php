<?php
/**
 * This file is part of phal framework.
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 * @copyright Copyright (c) 2013 Antonio Parraga Navarro
 * @link      http://aparraga.github.com/phal
 * @package   Phal
 * @license   http://aparraga.github.com/phal/license.html
 * @version   [version]
 */

__Phal::getInstance()->startup();

/**
 * This is the Phal engine class.
 * 
 * It exposes the {@link startup()} method to bootstrap the application context and call the request dispatcher.
 * 
 * It also loads the runtime directives used by Phal.
 *
 */
final class __Phal {

    static private $_instance = null;
    
    private $_started = false;
    private $_runtime_directives = null;
    private $_status = self::STATUS_STOPPED; 
    
    const STATUS_STOPPED = 0;
    const STATUS_LOADING = 1;
    const STATUS_RUNNING = 2;
    
    private function __construct() {
    }
    
    /**
     * Get the singleton instance of __Phal
     *
     * @return __Phal
     */
    final static public function &getInstance() {
        if(self::$_instance == null) {
            self::$_instance = new __Phal();
        }
        return self::$_instance;
    }
    
    /**
     * Get current runtime directives.
     *
     * @return __RuntimeDirectives
     */
    final public function &getRuntimeDirectives() {
        return $this->_runtime_directives;
    }
    
    final public function getStatus() {
        return $this->_status;
    }
    
    /**
     * Starts the Phal engine. 
     * 
     * This method is called automatically by just including the current file.
     *
     */
    final public function startup() {
        if( $this->_started == false ) {
            $this->_started = true;
            $this->_status  = self::STATUS_LOADING;
            $this->_startupPhalCore();
            __ContextManager::getInstance()->createApplicationContext();
            $this->_status  = self::STATUS_RUNNING;
            if(PHAL_AUTODISPATCH_CLIENT_REQUEST == true) {
                __FrontController::getInstance()->dispatchClientRequest();
            }
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('ERR_PHAL_ENGINE_ALREADY_STARTED');
        }
    }
    
    private function _startupPhalCore() {
        
        //Include phal constants:
        include 'core' . DIRECTORY_SEPARATOR . 'Constants.inc';
        
        //Do not process HEAD request if PHAL_IGNORE_HEAD_REQUEST constant is set to true
        if(key_exists('REQUEST_METHOD', $_SERVER) && $_SERVER['REQUEST_METHOD'] == 'HEAD' && PHAL_IGNORE_HEAD_REQUEST == true) {
            exit;
        }
        
        //Include runtime directives:
        include PHAL_CORE_DIR . DIRECTORY_SEPARATOR . 'RuntimeDirectives.class.php';
        $this->_runtime_directives = new __RuntimeDirectives();
        
        //Include bootstrap classes:
        include PHAL_CORE_DIR  . DIRECTORY_SEPARATOR . 'FileLocator.class.php';
        include PHAL_CORE_DIR  . DIRECTORY_SEPARATOR . 'FileResolver.class.php';
        include PHAL_CACHE_DIR . DIRECTORY_SEPARATOR . 'Cache.class.php';
        include PHAL_CACHE_DIR . DIRECTORY_SEPARATOR . 'CacheManager.class.php';
        include PHAL_CACHE_DIR . DIRECTORY_SEPARATOR . 'ICacheHandler.interface.php';
        include PHAL_CACHE_DIR . DIRECTORY_SEPARATOR . 'CacheHandler.class.php';
        include PHAL_CACHE_DIR . DIRECTORY_SEPARATOR . 'CacheHandlerFactory.class.php';        
        include PHAL_CORE_DIR  . DIRECTORY_SEPARATOR . 'ClassLoader.class.php';
        
        //load framework includepath:
        __ClassLoader::getInstance()->addClassFileLocator(new __ClassFileLocator(PHAL_DIR));

        include PHAL_CORE_DIR . DIRECTORY_SEPARATOR . 'ErrorHandling.php';
    }
    
}
