<?php

/**
 * Generic DAO used to perform data storage operations on collections.
 * It delegates into another DAO to perform data storage operations on individual items 
 *
 */
abstract class __AbstractCollectionDao extends __AbstractDao {

	protected $_collection_class = null;
	protected $_default_role = null;
	protected $_default_fk = null;
	
	public function setCollectionClass($collection_class) {
		$this->_collection_class = $collection_class;
	}
	
	public function setDefaultFK($default_fk) {
	    $this->_default_fk = $default_fk;
	}
	
	public function getDefaultFK() {
	    return $this->_default_fk;
	}

    /**
     * Loads a Collection for a given identifier
     *
     * @param string $auction_id
     * @param integer $limit
     * @param integer $offset
     * 
     * @return __Collection
     */
    public function &load($related_id, $limit = -1, $offset = -1) {
    	if($this->_default_fk != null) {
            $collection_class = $this->_collection_class;
            $return_value = new $collection_class();
            $table = $this->_table;
            $foreign_key = $this->_default_fk;
            $query="SELECT *
                    FROM $table
                    WHERE $foreign_key = ?";
            $connection = $this->_data_source->getConnection();
            $rs = $connection->SelectLimit($query, $limit, $offset, $related_id);
            foreach($rs as $row) {
                //create the user instance
                $item = call_user_func(array($this->_factory, 'createNonTransientInstance'), $row);
                $return_value->add($item);
                unset($item);
            }
            $return_value->setDirty(false);
            $return_value->setTransient(false);
    	}
    	else {
    		throw __ExceptionFactory::getInstance()->createException('Unknow default fk to retrieve ' . $this->_class . ' instances: ' . $related_role);
    	}
        return $return_value;
    }
    
    /**
     * Persist a given collection into the data source
     *
     * @param __Collection|VirtualProxy $collection
     */
    public function save(&$collection) {
        if( !$collection instanceof IPersistent ) {
            throw __ExceptionFactory::getInstance()->createException('A collection implementing the IPersistent was expected. It was given a ' . get_class($collection) . ' instance.');
        }     
      	if($collection instanceof VirtualProxy){
        	if($collection->isDirty()) {
            	$collection = $collection->getReceiver();
        	}else{
        		return;
        	}
        }
        
        if( ! $collection  instanceof $this->_collection_class ) {
            throw __ExceptionFactory::getInstance()->createException('Can not save instances of type ' . get_class($collection) . ' by ussing ' . get_class($this).'. A '.$this->_collection_class.' instance was expected');
        }
        
        __DaoManager::getInstance()->beginTransaction($this);
        try {
	        $dao = __DaoManager::getInstance()->getDao($this->_class);
	        $iterator = $collection->getIterator();
	        $iterator->first();
	        while(!$iterator->isDone()) {
	        	$item = $iterator->currentItem();
	        	$dao->save($item);
	        	$iterator->next();
	        }
	        $collection->setDirty(false);
	        $collection->setTransient(false);
        }
        catch (Exception $e) {
            __DaoManager::getInstance()->rollbackTransaction();
            throw $e;
        }
        __DaoManager::getInstance()->commitTransaction();        
    }	
	
}
