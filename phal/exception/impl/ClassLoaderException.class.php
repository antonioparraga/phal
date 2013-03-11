<?php

class __ClassLoaderException extends __PhalException { }

class __ClassLoaderExceptionRetranslator extends __PhalException
{
   public function __construct($serializedException)
   {
       throw unserialize($serializedException);
   }
}