<?php

class __MemoryStreamStorage implements __IStreamStorage {
    
    private $_stream_mode = 'r';
    
    /**
     * Returns an octal representation of access mode for an stream.
     * Mode are based on an octal representation of read/write/execution where
     * each digit from left to right represent the owner, the group and other respectively.
     *
     * @return integer
     */
    protected function _getStreamMode() {
        return $this->_stream_mode;
    }

    protected function _setStreamMode($stream_mode) {
        $format = 'text';
        if(is_string($stream_mode) && !is_numeric($stream_mode)) {
            $mode = 0;
            foreach($stream_mode as $single_mode) {
                switch (strtoupper($single_mode)) {
                    case 'R':
                        $mode |= 100;
                        break;
                    case 'W':
                    case 'A':
                        $mode |= 200;
                        break;
                    case 'X':
                        $mode |= 400;
                        break;
                    case 'T':
                        $format = 'text';
                        break;
                    case 'B':
                        $format = 'binary';
                        break;
                }
            }
            $this->_stream_mode   = (int) $mode;
            $this->_stream_format = $format;
        }
        else {
            $this->_stream_mode = (int) $stream_mode;
        }
    }
    
    public function stat() {    
        $time = time();
        if($this->_getStreamContent() != null) {
            $size = strlen($this->_getStreamContent());
        }
        else {
            $size = 0;
        }
        $uid  = getmyuid();
        $gid  = getmygid();
        $mode = octdec(100000 + $this->_getStreamMode());

        $keys = array(
            'dev'     => 0,
            'ino'     => 0,
            'mode'    => $mode,
            'nlink'   => 0,
            'uid'     => $uid,
            'gid'     => $gid,
            'rdev'    => 0,
            'size'    => $size,
            'atime'   => $time,
            'mtime'   => $time,
            'ctime'   => $time,
            'blksize' => 0,
            'blocks'  => 0
        );
        $return_value = $keys + array_values($keys);
        return $return;
    }
    
}