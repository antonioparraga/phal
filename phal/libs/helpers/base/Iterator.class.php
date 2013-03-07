<?php

class __Iterator implements Iterator{
    
    /**
     * This is the internal array that the iterator will iterate throught
     *
     * @var array
     */
    protected $_collection = array();
    
    protected $_internal_point = 0;
    
    /**
     * This is the constructor method. It will initializate the internal array with a
     * reference to a specified array.
     *
     * @param array The array to inicializate the internal array to iterate to
     */
    public function __construct(&$collection) {
        if(is_array($collection)) {
            $this->_collection =& $collection;
        }
        else {
            //exception!
        }
    }
    
    /**
     * This method will back the internal pointer of the iterator to the first element of the array.
     * It also returns the first element or FALSE if the array is empty
     *
     * @return mixed The first element of the array or FALSE if the internal array is empty
     */
    public function &first() {
        $this->_internal_point = 0;
        $return_value = reset($this->_collection);
        return $return_value;
    }
    
    /**
     * This method has the same behaviour as the first method (in fact, is an alias of {@link first})
     *
     * @return mixed The first element of the array or FALSE if the internal array is empty
     */
    public function &rewind() {
        return $this->first();
    }
    
    /**
     * This method will back one position left the internal pointer of the iterator.
     * It will also return the previous place that's pointed to by the internal array pointer, or FALSE if there are no more elements. 
     *
     * @return mixed Returns the array value in the previous place that's pointed to by the internal array pointer, or FALSE if there are no more elements. 
     */
    public function &previous() {
        if($this->_internal_point > 0) {
            $this->_internal_point--;
        }
        $return_value = prev($this->_collection);
        return $return_value;
    }

    /**
     * This method advanced one position the internal pointer.
     * It also returns the next place that's pointed to by the internal array pointer, or FALSE  if there are no more elements. 
     *
     * @return mixed Return the array value in the next place that's pointed to by the interal array pointer, or FALSE  if there are no more elements. 
     */
    public function &next() {
        if($this->_internal_point < count($this->_collection)) {
            $this->_internal_point++;
        }
        $return_value = next($this->_collection);
        return $return_value;
    }

    /**
     * This method advanced the internal pointer to the last position of the array
     *
     * @return mixed Return the last element of the array, or FALSE if the array is empty.
     */
    public function &last() {
        $this->_internal_point = count($this->_collection);
        $return_value = end($this->_collection);
        return $return_value;
    }
    
    /**
     * This method returns true or false depending on if the internal pointer is beyond the end of the array.
     *
     * @return boolean true if the internal pointer is beyond the end of the array.
     */
    public function isDone() {
        $return_value = false;
        if($this->_internal_point >= count($this->_collection)) {
            $return_value = true;
        }
        return $return_value;
    }
    
    /**
     * Alias of isDone
     * 
     * @return boolean
     *
     */
    public function valid() {
        return !$this->isDone();
    }
    
    /**
     * This method returns the element in the current position of the internal pointer.
     * It does not move the pointer in any way. If the internal pointer points beyond the
     * end of the elements list, currentItem returns FALSE. 
     *
     * @return mixed The current pointed element, or FALSE if the internal pointer points beyond the end of the elements list.
     */
    public function &currentItem() {
        $return_value = false;
        if(!$this->isDone()) {
            $return_value = current($this->_collection);
        }
        return $return_value;
    }
    
    /**
     * Alias of currentItem
     * 
     * @return mixed
     */
    public function &getCurrent() {
        return $this->currentItem();
    }

    /**
     * Alias of currentItem
     * @return mixed
     */
    public function &getCurrentItem() {
        return $this->currentItem();
    }    
    
    /**
     * Alias of currentItem
     * @return mixed
     */
    public function &current() {
        return $this->currentItem();
    }
    
    /**
     * This method returns the key of the element in the current position of the internal pointer.
     * It does not move the pointer in any way. If the internal pointer points beyond the
     * end of the elements list, currentItem returns FALSE. 
     *
     * @return mixed The current pointed element key, or FALSE if the internal pointer points beyond the end of the elements list.
     */
    public function &currentKey() {
        $return_value = false;
        if(!$this->isDone()) {
            $return_value = key($this->_collection);
        }
        return $return_value;
    }   

    /**
     * Alias of currentKey 
     * 
     * @return mixed The current pointed element key, or FALSE if the internal pointer points beyond the end of the elements list.
     */
    public function &key() {
        return $this->currentKey();
    }
    
}
