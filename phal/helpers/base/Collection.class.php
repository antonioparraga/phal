<?php

/**
 * Base class for collections.
 *
 */
class __Collection implements IteratorAggregate, ArrayAccess, Countable {

    protected $_collection = array();
    
    /**
     * This variable stores the class name of the iterator to be returned by the {@link getIterator} method
     *
     * @var string
     */
    protected $_iterator_class = '__Iterator';
    
    /**
     * This method adds an item in the collection. 
     * If a key is specified, the item is able to be identified by this key.
     * If no keys are specified, this method has the same behaviour as {@link Append}.
     *
     * @param mixed The item to add to the collection
     * @param mixed A key to identify the item in the collection
     */
    public function add(&$item, $key = null) {
        if($key == null) {
            $this->_collection[] =& $item;
        }
        else {
            $this->_collection[$key] =& $item;
        }
    }
    
    /**
     * Adds all the items within the given collection 
     * @param array $collection
     */
    public function addAll($collection) {
    	foreach($collection as $key => &$value) {
    		$this->add($value, $key);
    	}
    }
    
    /**
     * This method retrieves an element from the collection
     *
     * @param mixed The index or key that identify the item to be retrieved
     * @return mixed The requested item
     */
    public function &get($index) {
        $return_value = null;
        if(key_exists($index, $this->_collection)) {
            $return_value =& $this->_collection[$index];
        }
        return $return_value;
    }
        
    /**
     * This method check if a key exists in the collection
     *
     * @param string The key to check
     * @return boolean true if the key exists in the collection, else false
     */
    public function hasKey($index) {
        return key_exists($index, $this->_collection);
    }    
    
    public function keys() {
        return array_keys($this->_collection);
    }
    
    /**
     * This method delete an item from the collection
     *
     * @param mixed The index or key that identify the item to delete
     * @return bollean true if the item has been found and delete, else false
     */
    public function del($index) {
        $return_value = false;
        if(key_exists($index, $this->_collection)) {
            unset($this->_collection[$index]);
            $return_value = true;
        }
        return $return_value;
    }

    /**
     * This method returns the number of elements contained in the collection
     *
     * @return integer The number of elements contained in the collection
     */
    public function count() {
        $return_value = count($this->_collection);
        return $return_value;
    }

    /**
     * This method returns an iterator for the collection.
     * If this method is not overrided, it will return an instance of _iterator_class (if _iterator_class has
     * been setted and is correct).
     * 
     * @return Traversable An iterator for the collection
     */
    public function &getIterator() {
        $return_value = null;
        if(class_exists($this->_iterator_class)) {
            $return_value = new $this->_iterator_class($this->_collection);
        }
        return $return_value;
    }
    
    /**
     * Returns an array with all items contained in the current collection
     *
     * @return array
     */
    public function &toArray() {
        return $this->_collection;
    }

    /**
     * Removes all elements on current collection
     *
     */
    public function clear() {
        unset($this->_collection);
        $this->_collection = array();
    }
    
    /**
     * Populates the current collection from a given array of items
     * Note: This method does not call internally to the add method for each item
     *
     * @param array $items
     */
    public function fromArray(array &$items) {
        $this->clear();
        foreach($items as $key => &$item) {
            $this->add($item, $key);
        }
    }
    
    public function sort($cmp_function) {
        usort ($this->_collection, $cmp_function );
    }
    
    public function offsetSet($offset, $value) {
        $this->_collection[$offset] = $value;
    }
    
    public function offsetExists($offset) {
        return isset($this->_collection[$offset]);
    }
    
    public function offsetUnset($offset) {
        unset($this->_collection[$offset]);
    }
    
    public function offsetGet($offset) {
        return isset($this->_collection[$offset]) ? $this->_collection[$offset] : null;
    }    
    
}