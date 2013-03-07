<?php

class __ClientDataCollectorComponent extends __UIComponent implements __IPoolable {

    public function getData() {
        return $this->getProperties();
    }
    
    public function setData($data) {
        $this->setProperties($data);
    }
    
}
