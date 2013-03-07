<?php

interface __IDao {
    
    public function setDataSource(DataSource $data_source);
    
    public function &getDataSource();
    
}