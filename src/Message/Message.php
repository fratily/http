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
 *
 */
class Message implements MessageInterface{

    const PROTOCOL_VERSION  = [
        "1.0"   => 1,
        "1.1"   => 2,
        "2"     => 4
    ];

    const REGEX_NAME    = "/\A[0-9a-z-!#$%&'*+.^_`|~]+\z/i";

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
     * ヘッダーリストをバリデーションする
     *
     * @param   mixed[]
     *
     * @return  mixed[]|bool
     */
    private static function validHeaders(array $headers){
        $return = [];

        foreach($headers as $name => $values){
            if(!self::validName($name)){
                return false;
            }

            $return[$name]  = [];

            foreach((array)$values as $value){
                if(!is_scalar($value) || !self::validValue($value)){
                    return false;
                }

                $return[$name][]    = (string)$value;
            }
        }

        return $return;
    }

    private static function validName(string $name){
        return (bool)preg_match(self::REGEX_NAME, $name);
    }

    private static function validValue(string $value){
        return (bool)preg_match(self::REGEX_VALUE, $value);
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
        if(($headers = self::validHeaders($headers)) === false){
            throw new \InvalidArgumentException();
        }else if(!isset(self::PROTOCOL_VERSION[$version])){
            throw new \InvalidArgumentException();
        }

        foreach($headers as $name => $values){
            $lname  = strtolower($name);
            if(!isset($this->headerKeys[$lname])){
                $this->headerKeys[$lname] = $name;
            }

            $this->headers[$this->headerKeys[$lname]] = $values;
        }

        $this->body     = $body;
        $this->version  = $version;
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion(){
        return $this->protVer;
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
        if(!is_string($name)){
            throw new \InvalidArgumentException();
        }

        return isset($this->headerKeys[strtolower($name)]);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($name){
        if(!is_string($name)){
            throw new \InvalidArgumentException();
        }else if(!$this->hasHeader($name)){
            return [];
        }

        return $this->headers[$this->headerKeys[strtolower($name)]];
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderLine($name){
        if(!is_string($name)){
            throw new \InvalidArgumentException();
        }

        return implode(",", $this->getHeader($name));
    }

    /**
     * {@inheritdoc}
     */
    public function withHeader($name, $value){
        if(!is_string($name)){
            throw new \InvalidArgumentException();
        }else if(!is_array($value) && !is_scalar($value)){
            throw new \InvalidArgumentException();
        }else if(($header = self::validHeaders([$name => $value]))){
            throw new \InvalidArgumentException();
        }

        $return = clone $this;
        $lname  = strtolower($name);

        if(isset($return->headerKeys[$lname])){
            unset($return->headers[$return->headerKeys[$lname]]);
        }

        $this->headerKeys[$lname]   = $name;
        $return->headers[$name]     = $values;

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function withAddedHeader($name, $value){
        if(!is_string($name)){
            throw new \InvalidArgumentException();
        }else if(!$this->hasHeader($name)){
            return $this->withHeader($name, $value);
        }else if(!is_array($value) && !is_scalar($value)){
            throw new \InvalidArgumentException();
        }else if(($header = self::validHeaders([$name => $value]))){
            throw new \InvalidArgumentException();
        }

        $return = clone $this;
        $lname  = strtolower($name);
        $return->headers[$lname]    = array_merge(
            $return->headers[$lname],
            $header
        );

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutHeader($name){
        if(!is_string($name)){
            throw new \InvalidArgumentException();
        }else if($this->hasHeader($name)){
            return $this;
        }else if(!self::validName($name)){
            throw new \InvalidArgumentException();
        }

        $return = clone $this;
        $lname  = strtolower($name);

        unset($return->headers[$return->headerKeys[$lname]]);
        unset($return->headerKeys[$lname]);

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