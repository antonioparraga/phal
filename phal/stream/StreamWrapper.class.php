<?php

abstract class __StreamWrapper {

    protected $_stream_storage = null;
        
    public function stream_open($path, $mode, $options, $opened_dir )
    {
        $return_value = true; //by default
        $this->_stream_storage =& $this->_createStreamStorage($path);
        if( $this->_stream_storage instanceof __StreamStorage ) {
            $this->_stream_storage->open($mode);
        }
        else {
            $return_value = false;
            throw new __StreamException("Unable to create an stream storage for path: '" . $path . "'");
        }
        return $return_value;
    }

    public function stream_read($length)
    {
        return $this->_stream_storage->read($length);
    }

    public function stream_write($data, $length = null)
    {
        return $this->_stream_storage->write($data, $length);
    }
    
    public function stream_close() {
        return $this->_stream_storage->close();
    }

    public function stream_tell()
    {
        return $this->_stream_storage->tell();
    }
    
    public function stream_flush() {
        return $this->_stream_storage->flush();
    }

    public function stream_eof()
    {
        return $this->_stream_storage->eof();
    }

    public function stream_seek($offset, $whence = null)
    {
        return $this->_stream_storage->seek($offset, $whence);
    }
    
    public function stream_lock($operation) {
        return $this->_stream_storage->lock($operation);
    }

    public function stream_stat() {
        $this->_stream_storage->stat();
    }

    public function url_stat($path, $options) {
        $return_value = null;
        $stream_storage =& $this->_createStreamStorage($path);
        if( $stream_storage instanceof __StreamStorage ) {
            $return_value = $stream_storage->url_stat();
        }
        else {
            throw new __StreamException("Unable to create an stream storage for path: '" . $path . "'");
        }
        return $return_value;
    }

    abstract protected function &_createStreamStorage($path);

}