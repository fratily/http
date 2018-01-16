<?php
/**
 * FratilyPHP Http
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author      Kento Oka <kento.oka@kentoka.com>
 * @copyright   (c) Kento Oka
 * @license     MIT
 * @since       1.0.0
 */
namespace Fratily\Http\Message;

use Psr\Http\Message\StreamInterface;

/**
 *
 */
class Stream implements StreamInterface{

    /**
     * @var resource|null
     * @todo    このクラスを継承したクラスにもこれへのアクセスを許可する
     *          ただし、型はきちんと守らせる必要がある
     */
    private $resource;

    /**
     * Create new instance for stdin stream
     * 
     * @return  Stream\StdinStream
     */
    public static function stdin(){
        return new Stream\StdinStream();
    }
    
    /**
     * Create new instance for stdout stream
     * 
     * @return  static
     */
    public static function stdout(){
        return new static(STDOUT);
    }
    
    /**
     * Create new instance for memory stream
     * 
     * @param   string  $mode
     * 
     * @return  static
     */
    public static function memory(string $mode = "wb+"){
        return static::fromPath("php://memory", $mode);
    }
    
    /**
     * Create new instance for temp stream
     * 
     * @param   string  $mode
     * 
     * @return  static
     */
    public static function temp(string $mode = "wb+"){
        return static::fromPath("php://temp", $mode);
    }
    
    /**
     * Create new instance from file path
     *
     * @param   string  $path
     * @param   string  $mode
     *
     * @return  static
     *
     * @throws  Exception\InvalidStreamReferencedException
     */
    public static function fromPath(string $path, string $mode = "r"){
        $resource   = fopen($path, $mode);

        if($resource === false){
            //  TODO: fopenのエラーメッセージを受け取る方法を調べる
            throw new Exception\InvalidStreamReferencedException();
        }

        return new static($resource);
    }

    /**
     * Constructor
     *
     * @param   resource    $resource
     *
     * @throws  \InvalidArgumentException
     * @throws  Exception\InvalidStreamReferencedException
     */
    public function __construct($resource){
        if(!is_resource($resource)){
            throw new \InvalidArgumentException();
        }else if(get_resource_type($resource) !== "stream"){
            throw new Exception\InvalidStreamReferencedException();
        }

        $this->resource = $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(){
        $return = "";
        $offset = null;

        try{
            if($this->isReadable()){
                if($this->isSeekable()){
                    $offset = $this->tell();
                    $this->rewind();
                }

                $return = $this->getContents();
            }
        }catch(Exception $e){

        }finally{
            if($offset !== null){
                $this->seek($offset, SEEK_SET);
            }
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function close(){
        $resource   = $this->detach();

        if(is_resource($resource)){
            fclose($resource);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function detach(){
        $return = $this->resource;
        $this->resource = null;

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize(){
        if($this->resource === null){
            return null;
        }

        return fstat($this->resource)['size'] ?? null;
    }

    /**
     * {@inheritdoc}
     *
     * @throws  Exception\UnusableException
     */
    public function tell(){
        if($this->resource === null){
            throw new Exception\UnusableException();
        }else if(($point = ftell($this->resource)) === false){
            throw new \RuntimeException;
        }

        return $point;
    }

    /**
     * {@inheritdoc}
     */
    public function eof(){
        if($this->resource === null){
            return true;
        }

        return feof($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable(){
        if($this->resource !== null){
            return (bool)($this->getMetadata("seekable") ?? false);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @throws  \InvalidArgumentException
     * @throws  Exception\UnusableException
     * @throws  Exception\UnseekableException
     */
    public function seek($offset, $whence = SEEK_SET){
        if(!is_int($offset)){
            throw new \InvalidArgumentException();
        }else if(in_array($whence, [SEEK_SET, SEEK_CUR, SEEK_END])){
            throw new \InvalidArgumentException();
        }else if($this->resource === null){
            throw new Exception\UnusableException();
        }else if(!$this->isSeekable()){
            throw new Exception\UnseekableException();
        }

        if(fseek($this->resource, $offset, $whence) !== 0){
            throw new \RuntimeException;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws  Exception\UnusableException
     * @throws  Exception\UnseekableException
     */
    public function rewind(){
        return $this->seek(0, SEEK_SET);
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable(){
        if($this->resource !== null){
            $mode   = $this->getMetadata("mode");

            if($mode !== null){
                return strpos($mode, "x") !== false
                    || strpos($mode, "w") !== false
                    || strpos($mode, "a") !== false
                    || strpos($mode, "c") !== false
                    || strpos($mode, "+") !== false;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @throws  \InvalidArgumentException
     * @throws  Exception\UnusableException
     * @throws  Exception\UnwritableException
     */
    public function write($string){
        if(!is_scalar($string)){
            throw new \InvalidArgumentException();
        }else if($this->resource === null){
            throw new Exception\UnusableException();
        }else if(!$this->isWritable()){
            throw new Exception\UnwritableException();
        }

        $bytes  = fwrite($this->resource, (string)$string);

        if($bytes === false){
            throw new \RuntimeException();
        }

        return $bytes;
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable(){
        if($this->resource !== null){
            $mode   = $this->getMetadata("mode");

            if($mode !== null){
                return strpos($mode, "r") !== false
                    || strpos($mode, "+") !== false;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @throws  \InvalidArgumentException
     * @throws  Exception\UnusableException
     * @throws  Exception\UnreadableException
     */
    public function read($length){
        if(!is_int($length)){
            throw new \InvalidArgumentException();
        }else if($this->resource === null){
            throw new Exception\UnusableException();
        }else if(!$this->isReadable()){
            throw new Exception\UnreadableException();
        }

        $contents   = fread($this->resource, $length);

        if($contents === false){
            throw new \RuntimeException();
        }

        return $contents;
    }

    /**
     * {@inheritdoc}
     *
     * @throws  Exception\UnusableException
     * @throws  Exception\UnreadableException
     */
    public function getContents(){
        if($this->resource === null){
            throw new Exception\UnusableException();
        }else if(!$this->isReadable()){
            throw new Exception\UnreadableException();
        }

        $contents   = stream_get_contents($this->resource);

        if($contents === false){
            throw new \RuntimeException();
        }

        return $contents;
    }

    /**
     * {@inheritdoc}
     *
     * @throws  \InvalidArgumentException
     * @throws  Exception\UnusableException
     */
    public function getMetadata($key = null){
        if($key !== null && !is_string($key)){
            throw new \InvalidArgumentException();
        }else if($this->resource === null){
            throw new Exception\UnusableException();
        }

        $meta   = stream_get_meta_data($this->resource);

        if($key !== null){
            $meta   = $meta[$key] ?? null;
        }

        return $meta;
    }
}