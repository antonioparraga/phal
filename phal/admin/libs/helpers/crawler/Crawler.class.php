<?php

class __Crawler {
    
    protected $_uri = null;
    
    public function __construct() {
        $this->_home_uri = 'http://' . $_SERVER['HTTP_HOST'] . APP_URL_PATH;
    }
    
    public function crawl($ping_callback = null) {
        //reset the database:
        
        //crawl the site:
        $this->_doCrawl($this->_home_uri, $ping_callback);
    }
    
    protected function _doCrawl($uri, $ping_callback = null) {
        //crawl all the site and build the sitemap
        $page_visitor = new __PageVisitor();
        $page = $page_visitor->visit($uri, 1);
        $sitemap = __SiteMap::getInstance();
        if($page !== null) {
            $links = $page->getLinks();
            $counter = 0;
            while(count($links) > 0) {
                $link = array_shift($links);
                if($link->getLevel() <= 3) {
                    $href = $link->getHref();
                    $url_parts = parse_url($href);
                    //do not crawl outside our domain:
                    if($url_parts['host'] == $_SERVER['HTTP_HOST']) {
                        $page = $page_visitor->visit($link->getHref(), $link->getLevel() + 1);
                        if($page !== null) {
                            $sitemap->addPage($page);
                            $page_links = $page->getLinks();
                            foreach($page_links as $page_link) {
                                $page_link_href = $page_link->getHref();
                                if(!$sitemap->hasPage($page_link_href)) {
                                    if($page_link->getLevel() <= 3) {
                                        $links[$page_link_href] = $page_link;
                                    }
                                }
                            }
                            $counter++;
                            //callback each 100 links:
                            if($counter >= 5) {
                                $counter = 0;
                                if($ping_callback != null) {
                                    call_user_func_array($ping_callback, array(5, count($links)));
                                }
                            }
                            unset($page);
                        }
                    }
                }
            }
        }
    }
    

}
