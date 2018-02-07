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

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 */
class ServerRequest extends Request implements ServerRequestInterface{
    
    /**
     * @var mixed[]
     */
    private $serverParams   = [];

    /**
     * @var UploadedFileInterface[]
     */
    private $uploadedFiles  = [];
    
    /**
     * @var mixed[]
     */
    private $cookieParams   = [];

    /**
     * @var mixed[]
     */
    private $queryParams    = [];

    /**
     * @var null|mixed[]
     */
    private $parsedBody;
    
    /**
     * @var mixed[]
     */
    private $attributes     = [];
    
    /**
     * UploadedFiles配列のバリデーションを行う
     * 
     * @param   mixed   $uploadedFiles
     * @return  bool
     */
    private static function validUploadedFiles($uploadedFiles){
        if(!is_array($uploadedFiles)){
            return false;
        }
        
        foreach($uploadedFiles as $file){
            if(is_array($file)){
                if(self::validUploadedFiles($file)){
                    return false;
                }
            }else if(!($file instanceof UploadedFileInterface)){
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * 
     * @param   string  $method
     * @param   UriInterface $uri
     * @param   mixed[] $headers
     * @param   StreamInterface|null    $body
     * @param   mixed[] $serverParams
     * @param   UploadedFileInterface[] $uploadedFiles
     * @param   mixed[] $cookieParams
     * @param   mixed[] $queryParams
     * @param   mixed[]|object|null $parsedBody
     * @param   string  $version
     * 
     * @throws  \InvalidArgumentException
     */
    public function __construct(
        string $method,
        UriInterface $uri,
        array $headers = [],
        StreamInterface $body = null,
        array $serverParams = [],
        array $uploadedFiles = [],
        array $cookieParams = [],
        array $queryParams = [],
        $parsedBody = null,
        string $version = "1.1"
    ){
        parent::__construct($method, $uri, $headers, $body, $version);
        
        if(($uploadedFiles = self::validUploadedFiles($uploadedFiles)) === false){
            throw new \InvalidArgumentException();
        }else if($parsedBody !== null && !is_array($parsedBody) && !is_object($parsedBody)){
            throw new \InvalidArgumentException();
        }
        
        $this->serverParams     = $serverParams;
        $this->uploadedFiles    = $uploadedFiles;
        $this->cookieParams     = $cookieParams;
        $this->queryParams      = $queryParams;
        $this->parsedBody       = $parsedBody;
    }

    /**
     * {@inheritdoc}
     */
    public function getServerParams(){
        return $this->serverParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getUploadedFiles(){
        return $this->uploadedFiles;
    }

    /**
     * {@inheritdoc}
     */
    public function withUploadedFiles(array $uploadedFiles){
        if(!self::validUploadedFiles($uploadedFiles)){
            throw new \InvalidArgumentException();
        }
        
        $return = clone $this;
        $return->uploadedFiles  = $uploadedFiles;
        
        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieParams(){
        return $this->cookieParams;
    }

    /**
     * {@inheritdoc}
     */
    public function withCookieParams(array $cookies){
        $return = clone $this;
        $return->queryParams    = $cookies;
        
        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryParams(){
        return $this->queryParams;
    }

    /**
     * {@inheritdoc}
     */
    public function withQueryParams(array $query){
        $return = clone $this;
        $return->queryParams    = $query;
        
        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function getParsedBody(){
        return $this->parsedBody;
    }

    /**
     * {@inheritdoc}
     */
    public function withParsedBody($data){
        if($parsedBody !== null && !is_array($parsedBody) && !is_object($parsedBody)){
            throw new \InvalidArgumentException();
        }
        
        $return = clone $this;
        $return->parsedBody = $data;
        
        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes(){
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     * 
     * @throws  \InvalidArgumentException
     */
    public function getAttribute($name, $default = null){
        if(!is_scalar($name)){
            throw new \InvalidArgumentException();
        }
        
        return array_key_exists($name, $this->attributes)
            ? $this->attributes[$name] : $default;
    }

    /**
     * {@inheritdoc}
     * 
     * @throws  \InvalidArgumentException
     */
    public function withAttribute($name, $value){
        if(!is_scalar($name)){
            throw new \InvalidArgumentException();
        }
        
        if(array_key_exists($name, $this->attributes)
            && $this->attributes[$name] === $value
        ){
            $return = $this;
        }else{
            $return = clone $this;
            $return->attributes[$name]  = $value;
        }
        
        return $return;
    }

    /**
     * {@inheritdoc}
     * 
     * @throws  \InvalidArgumentException
     */
    public function withoutAttribute($name){
        if(!is_scalar($name)){
            throw new \InvalidArgumentException();
        }
        
        if(!array_key_exists($name, $this->attributes)){
            $return = $this;
        }else{
            $return = clone $this;
            unset($return->attributes[$name]);
        }
        
        return $return;
    }
}