<?php

abstract class __AbstractDao implements __IDao {
    
    protected $_data_source = null;
    protected $_class = null;
    protected $_table = null;
    protected $_factory  = null;
    protected $_dao_services = array();
    
    public function setFactoryClass($factory) {
        $this->_factory = $factory;
    }
    
    public function setClass($class) {
        $this->_class = $class;
    }
    
    public function setDaoServices(array &$dao_services) {
        $this->_dao_services =& $dao_services;
    }
    
    public function &getDaoServices() {
        return $this->_dao_services;
    }
    
    /**
     * Used by the framework to setup the data source by ussing dependency injection capabilities
     *
     * @param DataSource $data_source
     */
    public function setDataSource(DataSource $data_source) {
        $this->_data_source = $data_source;
    }
    
    /**
     * Gets data source bound to current instance
     *
     * @return DataSource
     */
    public function &getDataSource() {
        return $this->_data_source;
    }
    
    public function setTable($table) {
        $this->_table = $table;
    }
    
    public function getTable() {
        return $this->_table;
    }

    /**
     * Loads a forum post instance for a givem identifier
     *
     * @param string $forum_post_id
     * @return ForumPost
     * 
     * @todo remember (at request level) returned instances in order to save the same selects.
     */
    public function &load($id) {
        $key = get_class($this) . 'load' . $id;
        if(key_exists('LOAD', $this->_dao_services)) {
            $params = func_get_args();
            $return_value = $this->__call('load', $params);
        }
        else {
            $return_value = null;
            $table = $this->_table;
            $query="SELECT *
                    FROM $table
                    WHERE id = ?";
            $connection = $this->_data_source->getConnection();
            $rs = $connection->Execute($query, array($id));
            if($rs->RecordCount() == 1) {
                $row = $rs->GetRowAssoc(false);
                $return_value = call_user_func(array($this->_factory, 'createNonTransientInstance'), $row);
            }
        }
        return $return_value;
    }
        
    /**
     * Persist a given instance into the data source
     *
     * @param IPersistent|VirtualProxy $instance
     * 
     * @throws Exception In case the transaction fails
     * 
     */
    public function save(&$instance) {
        if( !$instance instanceof IPersistent ) {
            throw __ExceptionFactory::getInstance()->createException('A class implementing the IPersistent was expected. It was given a ' . get_class($instance) . ' instance.');
        }
        if($instance instanceof VirtualProxy){
        	if($instance->isDirty()) {
            	$instance = $instance->getReceiver();
        	}else{
        		return;
        	}
        }
        
        
        if( ! $instance instanceof $this->_class ) {
            throw __ExceptionFactory::getInstance()->createException('Can not save instances of type ' . get_class($instance) . ' by ussing ' . get_class($this));
        }
        
        if(!__DaoManager::getInstance()->isAlreadyPersisted($instance)) {
            __DaoManager::getInstance()->beginTransaction($this);
        	__DaoManager::getInstance()->markAsPersisted($instance);
            $instance_id = $instance->getId(); 
        	$key = get_class($this) . 'load' . $instance_id;
            try {
                if($instance->isDirty()) {
                    //invalidate from DaoResultsRememberer:
                    if(PersistentInstancesCollection::getInstance()->hasPersistentInstance(get_class($instance), $instance_id)) {
                        PersistentInstancesCollection::getInstance()->removePersistentInstance(get_class($instance), $instance_id);
                    }
                    //save or update:
                    if($instance->isTransient()) {
                        $this->create($instance);
                        $instance->setTransient(false);
                    }
                    else{
                        $this->update($instance);
                    }
                    //reset the dirty flag:
                    $instance->setDirty(false);
                }
                $this->_saveAggregatedInstances($instance);
            }
            catch (Exception $e) {
                __DaoManager::getInstance()->rollbackTransaction();
                throw $e;
            }
            __DaoManager::getInstance()->commitTransaction();
        }
        
    }

    protected function _saveAggregatedInstances(&$instance) {
        $class_name = get_class($instance);
        $class = new ReflectionClass($class_name);
        $properties = $class->getProperties();
        foreach($properties as $property) {
            $property_name = $property->getName();
            $getter_method = 'get' . ucfirst(str_replace('_', '', $property_name));
            if(method_exists($instance, $getter_method)) {
                $property_value = $instance->$getter_method();
                
                if($property_value instanceof IPersistent) {                	
                    $dao = __DaoManager::getInstance()->getDao($property_value);
                    if($dao != null) {
                        $dao->save($property_value);
                    }
                    else {
                        //todo, raise or not an exception
                    }
                }
            }
        }
    }
    
    public function create(&$instance) {
    	//todo a generic create method
    }
    
    public function update(&$instance) {
    	//todo a generic update method
    }

    /**
     * In case we are calling a Dao Service, arguments are the following:
     * 
     * mixed modelservice($placeholder_value1, 
     *                    $placeholder_value2, 
     *                    ..., 
     *                    CriteriaCollection $filter_criteria = null, 
     *                    int $limit = -1, 
     *                    int $offset = -1)
     *
     * 
     * @param string $alias
     * @param array $arguments
     * @return mixed
     */
    public function __call($alias, $arguments) {
        $return_value = null;
        $alias = strtoupper($alias);
        if(key_exists($alias, $this->_dao_services)) {
            $dao_service =& $this->_dao_services[$alias];
            $key = md5(get_class($this) . $alias . print_r($arguments, true));
            if($dao_service->getCache()) {
                $cache = __ApplicationContext::getInstance()->getCache();
                $data = $cache->getData($key, $dao_service->getCacheTtl());
                if($data !== null) {
                    return $data;
                }
            }
            $query = $dao_service->getSqlStatement();
            $placeholders_count = substr_count($query, '?');
            $num_arguments = count($arguments);
            if($num_arguments >= $placeholders_count) {
                //set default values:
                $placeholder_values = array();
                $order_by = array();
                if($placeholders_count > 0) {
                    $placeholder_values = array_slice($arguments, 0, $placeholders_count);
                }
                @list($filter_criteria, $limit, $offset) = array_slice($arguments, $placeholders_count);
                $limit  = $limit !== null ? $limit : $dao_service->getLimit();
                $offset = $offset !== null ? $offset : $dao_service->getOffset();
                if($filter_criteria != null) {
                    if($filter_criteria instanceof CriteriaCollection) {
                        $select_part = $filter_criteria->getSqlSelectPart();
                        if($select_part != null) {
                            $query = preg_replace('/\s+FROM\s+/i', ', ' . $select_part . ' FROM ', $query); 
                        }
                        $where_part = $filter_criteria->getSqlWherePart();
                        $placeholder_values = array_merge($placeholder_values, $filter_criteria->getSqlPlaceholderValues());
                        if($where_part != null) {
                            $query = str_replace('{where_part}', ' WHERE ' . $where_part, $query);
                            $query = str_replace('{and_where_part}', ' AND (' . $where_part . ')', $query);
                        }
                        $order_by = $filter_criteria->getSqlOrderByPart();
                        if($order_by != null) {
                            $query = str_replace('{order_by}', ' ORDER BY ' . $order_by, $query);
                            $query = str_replace('{order_by_part}', ', ' . $order_by, $query);
                        }
                    }
                    else {
                        throw __ExceptionFactory::getInstance()->createException('Wrong argument type. It was expected a CriteriaCollection instance');
                    }
                }
                //clean-up non-populated placeholders from query string:
                $query = str_replace('{where_part}', '', $query);
                $query = str_replace('{and_where_part}', '', $query);
                $query = str_replace('{order_by}', '', $query);
                $query = str_replace('{order_by_part}', '', $query);
                $connection = $this->_data_source->getConnection();
                $rs = $connection->SelectLimit($query, $limit, $offset, $placeholder_values);

                //not set the FACTORY class/method to process each resultant row
                $domain = $dao_service->getDomain();
                if($domain == 'integer') {
                    $factory_class  = $this;
                    $factory_method = 'processIntegerValue'; 
                }
                else if($domain == 'string') {
                    $factory_class  = $this;
                    $factory_method = 'processStringValue'; 
                }
                else if($domain == 'array') {
                    $factory_class  = $this;
                    $factory_method = 'processArrayValue'; 
                }
                else {
                    $factory_class  = $this->_factory;
                    $factory_method = 'createNonTransientInstance';
                }
                
                //not process the result:
                if($this instanceof AbstractCollectionDao) {
                    $collection_class = $this->_collection_class;
                    $return_value = new $collection_class();
                    if($rs->RecordCount() > 0) {
                        foreach($rs as $row) {
                            $instance = call_user_func(array($factory_class, $factory_method), $row);
                            $return_value->add($instance);
                            unset($instance);
                        }
                    }
                }
                else if($dao_service->getCardinality() == DaoService::CARDINALITY_SINGLE) {
                    $return_value = null;
                    if($rs->RecordCount() == 1) {
                        $row = $rs->GetRowAssoc(false);
                        $return_value = call_user_func(array($factory_class, $factory_method), $row);
                    }
                    else if($rs->RecordCount() > 1) {
                        throw __ExceptionFactory::getInstance()->createException('More than one row returned by ' . $alias . ' method while it was expected one single row as the service has a SINGLE cardinality.');   
                    }
                }
                else {
                    $return_value = array();
                    if($rs->RecordCount() > 0) {
                        foreach($rs as $row) {
                            $instance = call_user_func(array($factory_class, $factory_method), $row);
                            $return_value[] = $instance;
                            unset($instance);
                        }
                    }
                }
                
                //now postfilter the result (if applicable)
                $postfilter = $dao_service->getPostFilter();
                if(!empty($postfilter)) {
                    if(method_exists($this, $postfilter)) {
                        $return_value = $this->$postfilter($return_value, $arguments);
                    }
                    else {
                        throw __ExceptionFactory::getInstance()->createException('Postfilter method does not exists: ' . $postfilter);
                    }
                }
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('Error in number of arguments in call to ' . $alias . '. It was expected at least ' . $placeholders_count . ' argument.');
            }
            if($dao_service->getCache()) {
                $cache->setData($key, $return_value, $dao_service->getCacheTtl());           
            }
        }
        else {
            throw __ExceptionFactory::getInstance()->createException('Dao Service not found on DAO ' . get_class($this) . ': ' . $alias);
        }
        
        return $return_value;
    }
    
    public function processIntegerValue(array $row) {
        $return_value = null;
        if(count($row) > 0) {
            //get the value
            $return_value = reset($row);
            //cast the value
            if(is_numeric($return_value)) {
                $return_value = (int) $return_value;
            }
            else {
                throw __ExceptionFactory::getInstance()->createException('It was expected a numeric value. Received: ' . $return_value);
            }
        }
        return $return_value;
    }
    
    public function processStringValue(array $row) {
        $return_value = null;
        if(count($row) > 0) {
            //get the value
            $return_value = reset($row);
            //cast the value
            $return_value = '' . $return_value;
        }
        return $return_value;
    }
    
    public function processArrayValue(array $row) {
        return $row;
    }   
    
}