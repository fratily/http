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
     * サーバーパラメータからHTTPリクエストヘッダーを抽出する
     * 
     * @param   mixed[] $server
     * 
     * @return  mixed[]
     */
    private static function extractionHeaders(array $server){
        $return = [];

        foreach($server as $key => $value){
            if((bool)preg_match("/\AHTTP_[0-9A-Z!#$%&'*+\-.^_`|~]+\z/", $key)){
                $key    = substr($key, 5);

                if(strlen($key) > 0){
                    $key    = implode(
                        "-", array_map("ucfirst", explode("_", strtolower($key)))
                    );

                    $return[$key]   = $value;
                }
            }
        }

        return $return;
    }
    
    /**
     * Constructor
     * 
     * @param   string  $method
     * @param   UriInterface $uri
     * @param   mixed[] $serverParams
     * @param   UploadedFileInterface[] $uploadedFiles
     * @param   mixed[] $cookieParams
     * @param   mixed[] $queryParams
     * @param   string  $version
     * 
     * @throws  \InvalidArgumentException
     */
    public function __construct(
        string $method,
        UriInterface $uri,
        array $serverParams = [],
        array $uploadedFiles = [],
        array $cookieParams = [],
        array $queryParams = [],
        string $version = "1.1"
    ){
        parent::__construct(
            $method,
            $uri,
            self::extractionHeaders($serverParams),
            ($body = new Stream\InputStream()),
            $version
        );
        
        if(($uploadedFiles = self::validUploadedFiles($uploadedFiles)) === false){
            throw new \InvalidArgumentException();
        }
        
        $this->serverParams     = $serverParams;
        $this->uploadedFiles    = $uploadedFiles;
        $this->cookieParams     = $cookieParams;
        $this->queryParams      = $queryParams;
        
        if(in_array($method, ["POST", "PUT", "PATCH"])
            && ($server["CONTENT_TYPE"] ?? "") === "application/x-www-form-urlencoded"
        ){
            $body->rewind();
            $content    = $body->getContents();
            $body->rewind();
            
            mb_parse_str($content, $this->parsedBody);
        }
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