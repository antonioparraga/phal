<?php

class __PageVisitor {

    public function __construct() {
        
    }
    
    public function visit($uri, $level) {
        $return_value = null;
        $sitemap = __SiteMap::getInstance();
        if(!$sitemap->hasPage($uri)) {
            $return_value = new __SiteMapPage($uri);
            $return_value->setLevel($level);
            if(function_exists('curl_init')) {
                $content = $this->_setPageContent($uri, $return_value);
            }
            else {
                $content = file_get_contents($uri);
                $return_value->setContent($content);
            }
            if(!empty($content)) {
                $links = $this->_readLinks($uri, $content, $level);
                //build the page:            
                $return_value->setLinks($links);
            }
        }
        return $return_value;
    }
    
    protected function _readLinks($uri, $source, $level) {
        $return_value = array();

        foreach(DOMDocument::loadHTML($source)->getElementsByTagName('a') as $a)
        {
            $a->getAttribute('href');
            if(!$a->hasAttribute('rel') || !preg_match('/nofollow/', $a->getAttribute('rel'))) {
                $href = $a->getAttribute('href');
                if(!preg_match('/^javascript/', $href)) {
                    $anchor_text = $a->textContent;
                    $href = __UrlHelper::resolveUrl($a->getAttribute('href'), 'http://' . $_SERVER['HTTP_HOST']);
                    $link = new __SiteMapLink();
                    $link->setLevel($level);
                    $link->setAnchorText('' . $anchor_text);
                    $link->setHref('' . $href);
                    $return_value[] = $link;
                    unset($link);
                    unset($a);
                }
            }
        }
        return $return_value;
    }
    
    private function _setPageContent($url, __SiteMapPage &$page) {
        $ch = curl_init();    // initialize curl handle
        curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);              // Fail on errors
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);    // allow redirects
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
        curl_setopt($ch, CURLOPT_PORT, $_SERVER['SERVER_PORT']);            //Set the port number
        curl_setopt($ch, CURLOPT_TIMEOUT, 15); // times out after 15s
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');

        $return_value = curl_exec($ch);
        if($return_value === false) {
            $info = curl_getinfo($ch);
            $page->setHttpCode($info['http_code']);
            $page->setContent(null);
        }
        else {
            $info = curl_getinfo($ch);
            $page->setHttpCode($info['http_code']);
            $page->setContent($return_value);
        }
        curl_close($ch);
        
        return $return_value;
    }    
    
    
}
