<?php

/**
 * This class represents a transaction. 
 * It's has been designed in order to be able to works with multiple DAOs, which is
 * the requisite for transactions within this system.
 *
 */
class __Transaction {
    
    protected $_data_sources = array();
    
    public function addDao(IDao &$dao) {
        $data_source = $dao->getDataSource();
        if(!key_exists($data_source->getId(), $this->_data_sources)) {
            $data_source->beginTransaction();
            $this->_data_sources[$data_source->getId()] =& $data_source;
        }
    }
    
    public function rollback() {
        foreach($this->_data_sources as &$data_source) {
            $data_source->rollbackTransaction();
        }
    }
    
    public function commit() {
        foreach($this->_data_sources as &$data_source) {
            $data_source->commitTransaction();
        }
    }
    
}