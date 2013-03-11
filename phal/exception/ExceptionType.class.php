<?php

final class __ExceptionType {
    
    const WARNING    = 0x00000001;
    const NOTICE     = 0x00000010;
    const VALIDATION = 0x00000100;
    const CRITICAL   = 0x00001000;

   // ensures that this class acts like an enum
   // and that it cannot be instantiated
   private function __construct(){}

   static public function getAllExceptionTypes() {
        return array(self::WARNING, 
                     self::NOTICE, 
                     self::VALIDATION, 
                     self::CRITICAL);
    }

}