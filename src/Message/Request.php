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

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 *
 */
class Request extends Message implements RequestInterface{

    const HTTP_METHODS  = [
        "GET"       => true,
        "HEAD"      => true,
        "POST"      => true,
        "PUT"       => true,
        "PATCH"     => true,
        "DELETE"    => true
    ];

    /**
     * @var string
     */
    private $method;

    /**
     * @var UriInterface
     */
    private $uri;

    /**
     * Constructor
     *
     * @param   string  $method
     * @param   UriInterface    $uri
     * @param   mixed[] $headers
     * @param   StreamInterface $body
     * @param   string  $version
     *
     * @throws  \InvalidArgumentException
     */
    public function __construct(
        string $method,
        UriInterface $uri,
        array $headers = [],
        StreamInterface $body = null,
        string $version = "1.1"
    ){
        if(!isset(self::HTTP_METHODS[$method])){
            throw new \InvalidArgumentException();
        }

        parent::__construct(
            $headers,
            $body ?? Stream::fromPath("php://memory"),
            $version
        );

        $this->method   = $method;
        $this->uri      = $uri;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestTarget(){
        $path   = $this->uri->getPath();
        $query  = $this->uri->getQuery();

        return $path . ($query !== "" ? "?{$query}" : "");
    }

    /**
     * {@inheritdoc}
     */
    public function withRequestTarget($requestTarget){
        if(!is_string($requestTarget)){
            throw new \InvalidArgumentException();
        }

        if(($pos = strpos($requestTarget, "?")) !== false){
            $path   = substr($requestTarget, 0, $pos);
            $query  = substr($requestTarget, $pos + 1);
        }else{
            $path   = $requestTarget;
            $query  = "";
        }

        if($this->uri->getPath() === $path && $this->uri->getQuery() === $query){
            $return = $this;
        }else{
            $return = clone $this;
            $uri    = $this->getUri()->withPath($path)->withQuery($query);
            $return->withUri($uri);
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod(){
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function withMethod($method){
        if(!is_string($method)){
            throw new \InvalidArgumentException();
        }else if(!isset(self::HTTP_METHODS[$method])){
            throw new \InvalidArgumentException();
        }

        if($this->method === $method){
            $return = $this;
        }else{
            $return = clone $this;
            $return->method = $method;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUri(){
        return $this->uri;
    }

    /**
     * {@inheritdoc}
     */
    public function withUri(UriInterface $uri, $preserveHost = false){
        if(!is_bool($preserveHost)){
            throw new \InvalidArgumentException();
        }

        if($this->uri === $uri){
            $return = $this;
        }else{
            $return = clone $this;
            $return->uri    = $uri;
        }

        $host   = $uri->getHost();
        $port   = $uri->getPort();

        if(!$preserveHost && $host !== ""){
            if($port !== null){
                $host   = "{$host}:{$port}";
            }

            $return = $return->withoutHeader("host")->withHeader("Host", $host);
        }

        return $return;
    }
}