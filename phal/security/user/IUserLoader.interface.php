<?php


interface __IUserLoader {
    
    public function &loadUser(__IUserIdentity $user_identity);
    
}