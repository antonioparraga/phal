<?php

interface __ISubmitter extends __IUriContainer {
    
    public function setLastRequest(__IRequest &$request);
    
    public function getLastRequest();
    
}
