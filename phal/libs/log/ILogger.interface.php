<?php

interface __ILogger {
    
    public function debug($string);

    public function info($string);
    
    public function warn($string);

    public function error($string);
    
    public function fatal($string);
    
}
