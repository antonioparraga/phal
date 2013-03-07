<?php

abstract class __DataSource {

    protected $_id = null;
    
    protected $_db_engine   = null;
    protected $_db_name     = null;
    protected $_db_host     = null;
    protected $_db_user     = null;
    protected $_db_password = null;

    protected $_connection = null;
    
    
    public function __sleep() {
        $this->disconnect();
        return array('_id', '_db_engine', '_db_name', '_db_host', '_db_user', '_db_password', '_connection');
    }    
    
    public function setId($id) {
        $this->_id = $id;
    }
    
    public function getId() {
        return $this->_id;
    }
    
    public function setDbEngine($db_engine) {
        $this->_db_engine = $db_engine;
    }
    
    public function getDbEngine() {
        return $this->_db_engine;
    }
    
    public function setDbName($db_name) {
        $this->_db_name = $db_name;
    }
    
    public function getDbName() {
        return $this->_db_name;
    }
    
    public function setDbHost($db_host) {
        $this->_db_host = $db_host;
    }
    
    public function getDbHost() {
        return $this->_db_host;
    }
    
    public function setDbUser($db_user) {
        $this->_db_user = $db_user;
    }
    
    public function getDbUser() {
        return $this->_db_user;
    }

    public function setDbPassword($db_password) {
        $this->_db_password = $db_password;
    }
    
    public function getDbPassword() {
        return $this->_db_password;
    }

    public function &getConnection() {
        if($this->_connection == null) {
            $this->connect();
        }
        return $this->_connection;
    }
    
    abstract public function connect();
    
    abstract public function disconnect();
    
    abstract public function beginTransaction();
    
    abstract public function rollbackTransaction();
    
    abstract public function commitTransaction();
    
}