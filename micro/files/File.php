<?php /** @WARNING: NOT USING!!!! */

namespace Micro\files;

abstract class File
{
    protected $stream;

    /**
     * Create connect to driver FileSystem
     *
     * @access public
     *
     * @param array $params Parameters array
     *
     * @result void
     * @abstract
     */
    abstract public function __construct( array $params=[] );

    /**
     * Destroy connect to driver FileSystem
     *
     * @access public
     *
     * @result void
     * @abstract
     */
    abstract public function __destruct();

/*
    // Заголовки для работы с потоком
    //* createStream
    //* readStream
    //* writeStream
    //* updateStream
    //* putStream

    // заголовки для работы с файлами
    //* has
    //* rename
    //* get
    //* put
    //* write
    //* update
    //* copy
    //* read
    //* delete
    //* getSize
    //* size
    //* readAndDelete
    //* listcontents
    //* touch
    //* createFile

    // заголовки для работы с метой
    //* mtime
    //* getMimeType
    //* getMetaData
    //* getAccessTime
    //* setAccessTime
    //* getTimestamp
    //* getVisibility
    //* setVisibility
    //* getGroup
    //* setGroup
    //* mimeType
    //* checksum

    // заголовки для работы с директориями
    //* createDir
    //* deleteDir
*/

    /**
     * Copy file from $sourcePath to $destinationPath
     *
     * @access public
     *
     * @param string $sourcePath Path to source file
     * @param string $destinationPath Path to destination file
     *
     * @return bool
     */
    abstract public function copy($sourcePath, $destinationPath);
    /**
     * Unlink file from $filePath
     *
     * @access public
     *
     * @param string $filePath File path
     *
     * @return mixed
     */
    abstract public function unlink($filePath);
    /**
     * Get free space on dir or filesystem
     *
     * @access public
     *
     * @param string $directory Directory path
     *
     * @return float
     */
    abstract public function disk_free_space($directory);
    /**
     * Get total space on directory or filesystem
     *
     * @access public
     *
     * @param string $directory Directory path
     *
     * @return float
     */
    abstract public function disk_total_space($directory);

    abstract public function file_exists($filePath); // bool
    abstract public function file_get_contents($filePath); // string
    abstract public function file_put_contents($filePath, $data); // int
    abstract public function file($filePath); // array
    abstract public function fileATime($filePath); // int
    abstract public function fileCTime($filePath); // int
    abstract public function fileGroup($filePath); // int
    abstract public function fileINode($filePath); // int


}