<?php

class __PdoSQLiteDataSource extends __DataSource {

    protected $_connection = null;
    protected $_file = null;
    
    public function __destruct()
    {
        $this->disconnect();
    }

    public function setFile($file) {
        $this->_file = $file;
    }
    
    public function getFile() {
        return $this->_file;
    }
    
    public function connect()
    {
        $connection = new PDO('sqlite:' . $this->_file);
        if($connection) {
            $this->_connection =& $connection;
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Can not connect to database (' . $this->getConnectionString() . ')');
        }
    }

    public function disconnect()
    {
        if($this->_connection != null) {
            $this->_connection = null;
        }
    }
    
    public function beginTransaction() {
        $this->getConnection()->StartTrans();
    }
    
    public function rollbackTransaction() {
        $this->getConnection()->FailTrans();
    }
    
    public function commitTransaction() {
        $this->getConnection()->CompleteTrans();
    }

}

