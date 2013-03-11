<?php


class __UsernameIdentity implements __IUserIdentity {
    
    private $_username = null;
    
    public function setUsername($username) {
        $this->_username = $username;
    }
    
    public function getUsername() {
        return $this->_username;
    }
    
}