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

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * @todo    ヘッダーのHostは必ず1行目になければならない（外部に任せるかこちらでやってしまうか）
 */
class Message implements MessageInterface{

    const PROTOCOL_VERSION  = [
        "1.0"   => 1,
        "1.1"   => 2,
        "2"     => 4
    ];

    const REGEX_NAME    = "/\A[0-9A-Z-!#$%&'*+.^_`|~]+\z/i";

    //  改行を含む値はRFC7230的には非推奨
    const REGEX_VALUE   = "/\A([\x21-\x7e]([\x20\x09]+[\x21-\x7e])?)*\z/";

    /**
     * @var string[]
     */
    private $headers    = [];

    /**
     * @var string[]
     */
    private $headerKeys = [];

    /**
     * @var StreamInterface
     */
    private $body;

    /**
     * @var string
     */
    private $version;

    /**
     * ヘッダーネームが正しいか確認する
     * 
     * @param   string  $name
     * 
     * @return  bool
     */
    private static function validName(string $name){
        return (bool)preg_match(self::REGEX_NAME, $name);
    }

    /**
     * ヘッダー値もしくはそのリストが正しいか確認する
     * 
     * @param   string[]|string $values
     * 
     * @return  bool
     */
    private static function validValue($values){
        foreach((array)$values as $value){
            if(!is_scalar($value) || !(bool)preg_match(self::REGEX_VALUE, (string)$value)){
                return false;
            }
        }
        
        return true;
    }

    /**
     * Constructor
     *
     * @param   mixed[] $headers
     * @param   StreamInterface    $body
     * @param   string  $version
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        array $headers,
        StreamInterface $body,
        string $version = "1.1"
    ){
        if(!isset(self::PROTOCOL_VERSION[$version])){
            throw new \InvalidArgumentException();
        }

        foreach($headers as $name => $values){
            if(!self::validName($name)){
                throw new \InvalidArgumentException("Invalid name");
            }else if(!self::validValue($values)){
                throw new \InvalidArgumentException("Invalid value");
            }
            
            $key    = strtolower($name);
            
            $this->headerKeys[$key] = $this->headerKeys[$key] ?? $name;
            
            if(isset($this->headers[$this->headerKeys[$key]])){
                foreach((array)$values as $value){
                    $this->headers[$this->headerKeys[$key]][]   = $value;
                }
            }else{
                $this->headers[$this->headerKeys[$key]] = (array)$values;
            }
        }

        $this->body     = $body;
        $this->version  = $version;
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion(){
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function withProtocolVersion($version){
        if(!isset(self::PROTOCOL_VERSION[$version])){
            throw new \InvalidArgumentException();
        }

        if($this->version === $version){
            $return = $this;
        }else{
            $return = clone $this;
            $return->version    = $version;
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders(){
        return $this->headers;
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader($name){
        if(!self::validName($name)){
            throw new \InvalidArgumentException("Invalid name");
        }

        return isset($this->headerKeys[strtolower($name)]);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($name){
        if(!$this->hasHeader($name)){
            return [];
        }
        
        if(!self::validName($name)){
            throw new \InvalidArgumentException("Invalid name");
        }

        return $this->headers[$this->headerKeys[strtolower($name)]];
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderLine($name){
        if(!self::validName($name)){
            throw new \InvalidArgumentException("Invalid name");
        }

        return implode(",", $this->getHeader($name));
    }

    /**
     * {@inheritdoc}
     */
    public function withHeader($name, $value){
        if(!self::validName($name)){
            throw new \InvalidArgumentException("Invalid name");
        }else if(!self::validValue($value)){
            throw new \InvalidArgumentException("Invalid value");
        }

        $return = clone $this;
        $key    = strtolower($name);
        
        if(isset($return->headerKeys[$key])){
            unset($return->headers[$return->headerKeys[$key]]);
            unset($return->headerKeys[$key]);
        }
        
        $return->headerKeys[$key]   = $name;
        $return->headers[$name]     = (array)$value;

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedHeader($name, $value){
        if(!$this->hasHeader($name)){
            return $this->withHeader($name, $value);
        }
        
        if(!self::validName($name)){
            throw new \InvalidArgumentException("Invalid name");
        }else if(!self::validValue($value)){
            throw new \InvalidArgumentException("Invalid value");
        }

        $return = clone $this;
        $key    = strtolower($name);
        
        foreach((array)$value as $v){
            $return->headers[$return->headerKeys[$key]][]   = $v;
        }
        
        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutHeader($name){
        if(!$this->hasHeader($name)){
            return $this;
        }
        
        if(!self::validName($name)){
            throw new \InvalidArgumentException("Invalid name");
        }

        $return = clone $this;
        $key    = strtolower($name);

        unset($return->headers[$return->headerKeys[$key]]);
        unset($return->headerKeys[$key]);

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody(){
        return $this->body;
    }

    /**
     * {@inheritdoc}
     */
    public function withBody(StreamInterface $body){
        if($this->body === $body){
            $return = $this;
        }else{
            $return = clone $this;
            $return->body   = $body;
        }

        return $return;
    }
}