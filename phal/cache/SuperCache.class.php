<?php

class __SuperCache {

	protected static $_instance = null;
	
	protected $_supercache_dir = null;
	
	public static function getInstance() {
		if(self::$_instance == null) {
			self::$_instance = new __SuperCache();
		}
		return self::$_instance;
	}

	public function setResponseToSuperCache(__IRequest &$request, __IResponse &$response) {
		//only cache anonymous view:
		if($response->isCacheable()) {
			$uri = $request->getUri();
			if($uri != null) {
				$route = $uri->getRoute();
				if($route != null) {
					$target_url_components = parse_url($uri->getAbsoluteUrl());
					$path = $route->getSuperCacheFile($uri);
					if($path == null) {
						$path = $target_url_components['path'];
					}
					$dir = dirname($path);
					if($dir == '.') {
						$dir = '';
					}
					$file = basename($path);
					$supercache_mark = $this->_getHtmlSupercacheMark($request, $response);
					$response_content = $response->getContent() . "\n" . $supercache_mark;
				
					//SUPERCACHE_DIR
					$server_dir = $this->getSuperCacheDir() . DIRECTORY_SEPARATOR . trim($dir, DIRECTORY_SEPARATOR);
					if(!is_dir($server_dir)) {
						mkdir($server_dir, 0777, true);
					}
					if(is_dir($server_dir) && is_writable($server_dir)) {
						$file_handler = fopen($server_dir . DIRECTORY_SEPARATOR . $file, "w+");
						fputs($file_handler, $response_content);
						fclose($file_handler);
					}
					else {
						$exception = __ExceptionFactory::getInstance()->createException('Directory not found to supercache: ' . $server_dir);
						__ErrorHandler::getInstance()->logException($exception);
					}
				}
			}
		}
	}
	
	
	protected function _getHtmlSupercacheMark(__IRequest &$request, __IResponse &$response) {
		 
		$mark_components = array();
		$uri = $request->getUri();
		if($uri != null) {
			$route = $uri->getRoute();
			if($route != null) {
				$cache_ttl = $route->getCacheTtl();
				if(!empty($cache_ttl)) {
					$date = new DateTime();
					$timestamp = $date->getTimestamp();
					$end_timestamp = $timestamp + $cache_ttl;
					$mark_components[] = '[te=' . $end_timestamp . ']';
				}
				$url = $uri->getUrl();
				$mark_components[] = '[url=' . $url . ']';
				$cache_groups = $route->getCacheGroups($uri);
				if(is_array($cache_groups)) {
					foreach($cache_groups as $cache_group) {
						$mark_components[] = '[cgr=' . $cache_group . ']';
					}
				}
			}
		}
		$return_value = "<!-- supercache " . implode(' ', $mark_components) . " -->\n";
		return $return_value;
	}
		
	public function getSuperCacheDir() {
		if($this->_supercache_dir == null) {
			if($_SERVER['DOCUMENT_ROOT'] != null) {
				$server_dir = rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR);
			}
			else {
				$server_dir = rtrim(__CurrentContext::getInstance()->getBaseDir(), DIRECTORY_SEPARATOR);
			}
			$phal_runtime_directives = __Phal::getInstance()->getRuntimeDirectives();
			//log exception just in case the runtime directive is enabled
			if($phal_runtime_directives->hasDirective('SUPERCACHE_DIR')) {
				$server_dir = $server_dir . DIRECTORY_SEPARATOR . trim($phal_runtime_directives->getDirective('SUPERCACHE_DIR'), DIRECTORY_SEPARATOR);
			}
			$this->_supercache_dir = $server_dir;
		}
		return $this->_supercache_dir;
	}
	
	
	public function invalidateGroup($group) {
		return $this->_removeSuperCacheFiles($this->getSuperCacheDir(), '[cgr=' . $group . ']');
	}
	
	protected function _removeSuperCacheFiles($current_dir, $matching_coincidence) {
		$return_value = 0;
		if(is_readable($current_dir) && is_dir($current_dir)) {
			$dir = dir($current_dir);
			while (false !== ($file = $dir->read())) {
				$current_file = $current_dir . DIRECTORY_SEPARATOR . $file;
				$position_of_dot = strpos($file, ".");
				if(is_readable($current_file) && $position_of_dot !== 0) {
					if(is_file($current_file)) {
						$file_content = file_get_contents($current_file);
						if(strpos($file_content, $matching_coincidence) !== false &&
						   strpos($file_content, 'supercache') !== false &&
						   strpos($current_dir, $this->getSuperCacheDir()) !== false) {
							//remove the file
							unlink($current_file);
							$return_value = $return_value + 1;
						}
					}
					else if(is_dir($current_file)) {
						$return_value = $return_value + $this->_removeSuperCacheFiles($current_file, $matching_coincidence);
					}
				}
			}
		}
		return $return_value;
	}
	
	
	
}

