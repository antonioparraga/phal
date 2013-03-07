<?php

class __PageRanker {

    protected $_sitemap = null;
    protected $_dump_factor = null;
    protected $_number_of_iterations = 100;
    protected $_rank_values = array();
    protected $_link_counts = array();
    
    public function __construct(__SiteMap &$sitemap, $dump_factor = 0.85) {
        $this->_sitemap =& $sitemap;
        $this->_dump_factor = $dump_factor;
    }
    
    public function setNumberOfIterations($number_of_iterations) {
        $this->_number_of_iterations = $number_of_iterations;
    }

    public function calculate() {
        $this->_prepare();
        for($i = 0; $i < $this->_number_of_iterations; $i++) {
            $this->_iterate();
        }
        //update ranks:
        foreach($this->_rank_values as $page_href => $rank) {
            if($this->_sitemap->hasPage($page_href)) {
                $this->_sitemap->getPage($page_href)->setRank($rank);
            }
        }
    }    

    protected function _prepare() {
        $this->_rank_values = array();
        $pages = $this->_sitemap->getPages();
        foreach($pages as $page) {
            $uri = $page->getUri();
            $this->_rank_values[$uri] = (float) (1 - $this->_dump_factor);
            $this->_link_counts[$uri] = count($page->getLinks());
        }
    }
    
    protected function _iterate() {
        $connection_matrix = $this->_sitemap->getConnectionMatrix();
        //work with a freezed rank matrix:
        $rank_values = $this->_rank_values;        
        foreach($connection_matrix as $page_url => $votes) {
            $transfered_pagerank = 0.0;
            foreach($votes as $linker_page => $dummy) {
                $pr_tn = $this->_rank_values[$linker_page];
                $c_tn  = $this->_link_counts[$linker_page];
                $transfered_pagerank += $pr_tn / $c_tn;
            }
            $rank_values[$page_url] = (float) ((1 - $this->_dump_factor) + ($this->_dump_factor * $transfered_pagerank));
        }
        $this->_rank_values = $rank_values;
    }
    
}

