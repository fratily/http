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
namespace Fratily\Http;

use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface{
    
    /**
     * @var resource|null
     */
    private $resource;
    
    public static function fromPath(string $path, string $mode){
        $resource   = fopen($path, $mode);
        
        if($resource === false){
            throw new \RuntimeException;
        }
        
        return new static($resource);
    }

    /**
     * 
     * 
     * @param   resource    $resource
     * @param   string  $mode
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
        if($this->isReadable()){
            try{
                if($this->isSeekable()){
                    $this->rewind();
                }
                
                return $this->getContents();
            }catch(Exception $e){
                
            }
        }
        
        return "";
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
    
    public function isClosed(){
        return $this->resource === null;
    }

    /**
     * {@inheritdoc}
     */
    public function detach(){
        $return = $this->resource;
        $this->resource = null;
        
        return $return;
    }
    
    public function attach($resource){
        if(!is_resource($resource)){
            throw new \InvalidArgumentException();
        }else if(get_resource_type($resource) !== "stream"){
            throw new \InvalidArgumentException();
        }
        
        $this->resource = $resource;
        
        return $this;
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
     */
    public function tell(){
        if($this->resource === null){
            throw new \RuntimeException;
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
            return $this->getMetadata("seekable") ?? false;
        }
        
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = SEEK_SET){
        if(!is_int($offset)){
            throw new \InvalidArgumentException();
        }else if($this->resource === null){
            throw new \RuntimeException;
        }else if(!$this->isSeekable()){
            throw new \RuntimeException;
        }

        if(fseek($this->resource, $offset, $whence) !== 0){
            throw new RuntimeException();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(){
        return $this->seek(0);
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
     */
    public function write($string){
        if(!is_scalar($string)){
            throw new \InvalidArgumentException();
        }else if($this->resource === null){
            throw new \RuntimeException;
        }else if(!$this->isWritable()){
            throw new \RuntimeException;
        }
        
        $bytes  = fwrite($this->resource, (string)$string);
        
        if($bytes === false){
            throw new \RuntimeException;
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
     */
    public function read($length){
        if(!is_int($length)){
            throw new \InvalidArgumentException();
        }else if($this->resource === null){
            throw new \RuntimeException;
        }else if(!$this->isReadable()){
            throw new \RuntimeException;
        }
        
        $contents   = fread($this->resource, $length);
        
        if($contents === false){
            throw new \RuntimeException;
        }

        return $contents;
    }

    /**
     * {@inheritdoc}
     */
    public function getContents(){
        if($this->resource === null){
            throw new \RuntimeException;
        }else if(!$this->isReadable()){
            throw new \RuntimeException;
        }
        
        $contents   = stream_get_contents($this->resource);
        
        if($contents === false){
            throw new \RuntimeException;
        }
        
        return $contents;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null){
        if($key !== null && !is_string($key)){
            throw new \InvalidArgumentException();
        }else if($this->resource === null){
            throw new \RuntimeException;
        }
        
        $meta   = stream_get_meta_data($this->resource);
        
        if($key !== null){
            $meta   = $meta[$key] ?? null;
        }
        
        return $meta;
    }
}