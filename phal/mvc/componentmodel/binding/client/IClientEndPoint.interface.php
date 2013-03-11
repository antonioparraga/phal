<?php

interface __IClientEndPoint extends __IEndPoint {
    
    public function getSetupCommand();

    public function getCommand();    
    
    public function synchronize(__IServerEndPoint &$client_end_point);
    
}