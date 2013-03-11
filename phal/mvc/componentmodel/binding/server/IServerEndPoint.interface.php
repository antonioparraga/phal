<?php

interface __IServerEndPoint extends __IEndPoint {
    
    public function getViewCode();
    
    public function &getComponent();    

    public function synchronize(__IClientEndPoint &$client_end_point);
    
}