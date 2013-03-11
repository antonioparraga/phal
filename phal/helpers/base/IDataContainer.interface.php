<?php

interface __IDataContainer {
    
    public function hasData($key);

    public function &getData($key);

    public function setData($key, &$data);

    public function removeData($key);
    
}
