<?php

namespace Micro\files;

class LocalDriver extends File
{
    /**
     * Create connect to driver FileSystem
     *
     * @access public
     *
     * @param array $params Parameters array
     *
     * @result void
     */
    public function __construct( array $params=[] )
    {
        $this->stream = true;
    }

    /**
     * Destroy connect to driver FileSystem
     */
    public function __destruct()
    {
        unset($this->stream);
    }

    public function file_exists( $filePath )
    {
        return \file_exists($filePath);
    }
}