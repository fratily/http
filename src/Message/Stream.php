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
     * Constructor
     *
     * @param   resource    $resource
     *
     * @throws  \InvalidArgumentException
     */
    public function __construct($resource){
        if(!is_resource($resource)){
            throw new \InvalidArgumentException();
        }else if(get_resource_type($resource) !== "stream"){
            throw new \InvalidArgumentException();
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

        return fstat($this->resource)["size"] ?? null;
    }

    /**
     * {@inheritdoc}
     *
     * @throws  Exception\StreamException
     */
    public function tell(){
        if($this->resource === null){
            throw Exception\StreamException::unavailable();
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
     * @throws  Exception\StreamException
     */
    public function seek($offset, $whence = SEEK_SET){
        if(!is_int($offset)){
            throw new \InvalidArgumentException();
        }else if(!in_array($whence, [SEEK_SET, SEEK_CUR, SEEK_END])){
            throw new \InvalidArgumentException();
        }else if($this->resource === null){
            throw Exception\StreamException::unavailable();
        }else if(!$this->isSeekable()){
            throw Exception\StreamException::unseekable();
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
     * @throws  Exception\StreamException
     */
    public function write($string){
        if(!is_scalar($string)){
            throw new \InvalidArgumentException();
        }else if($this->resource === null){
            throw Exception\StreamException::unavailable();
        }else if(!$this->isWritable()){
            throw Exception\StreamException::unwritable();
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
     * @throws  Exception\StreamException
     */
    public function read($length){
        if(!is_int($length)){
            throw new \InvalidArgumentException();
        }else if($this->resource === null){
            throw Exception\StreamException::unavailable();
        }else if(!$this->isReadable()){
            throw Exception\StreamException::unreadable();
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
     * @throws  Exception\StreamException
     */
    public function getContents(){
        if($this->resource === null){
            throw Exception\StreamException::unavailable();
        }else if(!$this->isReadable()){
            throw Exception\StreamException::unreadable();
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
     * @throws  Exception\StreamException
     */
    public function getMetadata($key = null){
        if($key !== null && !is_string($key)){
            throw new \InvalidArgumentException();
        }else if($this->resource === null){
            throw Exception\StreamException::unavailable();
        }

        $meta   = stream_get_meta_data($this->resource);

        if($key !== null){
            $meta   = $meta[$key] ?? null;
        }

        return $meta;
    }
}