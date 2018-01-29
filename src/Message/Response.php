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

use Fratily\Http\Status\HttpStatus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 *
 */
class Response extends Message implements ResponseInterface{

    /**
     * HTTP status code
     *
     * @var int
     */
    private $code;

    /**
     * 
     * 
     * @deprecated  これはファクトリーの仕事なのでいずれ分割する
     * 
     * @param   int $code
     * @param   mixed[] $headers
     * 
     * @return  static
     */
    public function emptyContents(int $code = 204, array $headers = []){
        return (new static($code, $headers, Stream::temp("r")));
    }
    
    /**
     * 
     * 
     * @deprecated  これはファクトリーの仕事なのでいずれ分割する
     * 
     * @param   string  $body
     * @param   int $code
     * @param   mixed[] $headers
     * @param   string  $charset
     * 
     * @return  static
     */
    public function text(
        string $body = "",
        int $code = 200,
        array $headers = [],
        string $charset = "utf-8"
    ){
        $stream = Stream::memory();
        $stream->write($body);
        
        return (new static($code, $headers, $stream))
            ->withContentType("text/plain; charset={$charset}");
    }
    
    /**
     * 
     * 
     * @deprecated  これはファクトリーの仕事なのでいずれ分割する
     * 
     * @param   string  $body
     * @param   int $code
     * @param   mixed[] $headers
     * @param   string  $charset
     * 
     * @return  static
     */
    public function html(
        string $body = "",
        int $code = 200,
        array $headers = [],
        string $charset = "utf-8"
    ){
        $stream = Stream::memory();
        $stream->write($body);
        
        return (new static($code, $headers, $stream))
            ->withContentType("text/html; charset={$charset}");
    }
    
    /**
     * 
     * 
     * @deprecated  これはファクトリーの仕事なのでいずれ分割する
     * 
     * @param   mixed[] $data
     * @param   int $code
     * @param   mixed[] $headers
     * 
     * @return  static
     */
    public function json(
        array $data = [],
        int $code = 200,
        array $headers = []
    ){
        $stream = Stream::memory();
        
        json_encode(null);
        $json   = json_encode($data);
        
        if(json_last_error() !== JSON_ERROR_NONE){
            throw new \RuntimeException(
                "Json encode error: " . json_last_error_msg()
            );
        }
        
        $stream->write($json);
        
        return (new static($code, $headers, $stream))
            ->withContentType("text/html; charset={$charset}");
    }
    
    /**
     * Constructor
     *
     * @param   int $code
     * @param   mixed[] $headers
     * @param   StreamInterface $body
     *
     * @throws  \InvalidArgumentException
     */
    public function __construct(
        int $code = 200,
        array $headers = [],
        StreamInterface $body = null
    ){
        if(!isset(HttpStatus::REASON_PHRASE[$code])){
            throw new \InvalidArgumentException();
        }if($body !== null && (!$body->isWritable() || !$body->isReadable())){
            throw new \InvalidArgumentException();
        }

        $this->code     = $code;
        $this->phrase   = HttpStatus::REASON_PHRASE[$code] ?? null;

        parent::__construct($headers, $body ?? new Stream\MemoryStream());
    }

    /**
     * {@inheritoc}
     */
    public function getStatusCode(){
        return $this->code;
    }

    /**
     * {@inheritoc}
     */
    public function getReasonPhrase(){
        return $this->phrase;
    }

    /**
     * {@inheritoc}
     */
    public function withStatus($code, $reasonPhrase = ""){
        if(!is_int($code) || !isset(HttpStatus::REASON_PHRASE[$code])){
            throw new \InvalidArgumentException();
        }else if($reasonPhrase !== "" && HttpStatus::REASON_PHRASE[$code] !== $reasonPhrase){
            throw new \InvalidArgumentException();
        }

        if($this->code === $code){
            $return = $this;
        }else{
            $return         = clone $this;
            $return->code   = $code;
        }

        return $return;
    }
    
    public function getContentType(){
        if(!$this->hasHeader("content-type")){
            return "";
        }
        
        return $this->getHeaderLine("content-type");
    }
    
    public function withContentType(string $contentType){
        if($this->hasHeader("content-type")
            && $this->getHeaderLine("content-type") === $contentType
        ){
            $return = $this;
        }else{
            $return = clone $this;
            $return = $return
                ->withoutHeader("content-type")
                ->withHeader("Content-Type", $contentType);
        }
        
        return $return;
    }
}