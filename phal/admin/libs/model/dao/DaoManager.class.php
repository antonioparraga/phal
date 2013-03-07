<?php

class __DaoManager {
    
    private $_daos = array();
    private $_transactions = null;
    private $_dao_services = array();
    private $_persisted_instances = array();
    
    public function __wakeup() {
        $this->_persisted_instances = array();
        $this->_transactions = new __Stack();
    }
     
    public function __construct() {
        $this->_transactions = new __Stack();
        $this->_dao_services = __ApplicationContext::getInstance()->getConfiguration()->getSection('dao-services');        
    }
    
    /**
     * Gets a singleton DaoManager context instance
     *
     * @return DaoManager
     */
    static public function &getInstance() {
        return __ApplicationContext::getInstance()->getContextInstance('daoManager');
    }
    
    public function setDaos(array $daos) {
        foreach($daos as $class_name => &$dao) {
            $this->addDao($dao, $class_name);
        }
    }
    
    public function addDao(IDao &$dao, $class_name) {
        $class_name = strtoupper($class_name);
        $dao_class  = strtoupper(get_class($dao));
        if(key_exists($dao_class, $this->_dao_services)) {
            $dao->setDaoServices($this->_dao_services[$dao_class]);
        }
        $this->_daos[$class_name] =& $dao;
    }
    
    public function &getDao($class_name) {
    	if(!is_string($class_name))  {
    		if($class_name instanceof VirtualProxy ){
    			$class_name = $class_name->getReceiverClass();
    		}else{
    			$class_name = get_class($class_name);
    		}
    	}
    	
    	
        $class_name = strtoupper($class_name);
        if(key_exists($class_name, $this->_daos)) {
            $return_value =& $this->_daos[$class_name];
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('DAO not found to load ' . $class_name . ' instances');
        }
        if($this->isUnderTransaction()) {
            $transaction = $this->_transactions->peek();
            $transaction->addDao($return_value);
        }
        return $return_value;
    }
    
    protected function _getUniqueKey(IPersistent &$instance) {
        return get_class($instance) . $instance->getId();
    }
    
    public function isAlreadyPersisted(IPersistent &$instance) {
        return key_exists($this->_getUniqueKey($instance), $this->_persisted_instances);
    }

    public function markAsPersisted(IPersistent &$instance) {
        $this->_persisted_instances[$this->_getUniqueKey($instance)] = true;
    }

    public function isUnderTransaction() {
        return $this->_transactions->count() > 0;
    }    
    
    /**
     * Enter description here...
     *
     * @param IDao Dao that has started the transaction
     * @return unknown
     */
    public function beginTransaction(IDao &$dao = null) {
        $transaction = new Transaction();
        if($dao != null) {
            $transaction->addDao($dao);
        }
        $this->_transactions->push($transaction);
        return $transaction;
    }
    
    public function rollbackTransaction() {
        if($this->isUnderTransaction()) {
            $transaction = $this->_transactions->pop();
            $transaction->rollback();
            if(!$this->isUnderTransaction()) {
                unset($this->_persisted_instances);
                $this->_persisted_instances = array();
            }
        }
    }
    
    public function commitTransaction() {
        if($this->isUnderTransaction()) {
            $transaction = $this->_transactions->pop();
            $transaction->commit();
            if(!$this->isUnderTransaction()) {
                unset($this->_persisted_instances);
                $this->_persisted_instances = array();
            }
        }
    }
    
}