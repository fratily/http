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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 *
 */
class Response extends Message implements ResponseInterface{

    /**
     * HTTP Status Codes
     *
     * @see https://tools.ietf.org/html/rfc7231
     * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     */
    const REASON_PHRASE = [
        100 => "Continue",
        101 => "Switching Protocols",
        102 => "Processing",
        103 => "Early Hints",
        200 => "OK",
        201 => "Created",
        202 => "Accepted",
        203 => "Non-Authoritative Information",
        204 => "No Content",
        205 => "Reset Content",
        206 => "Partial Content",
        207 => "Multi-Status",
        208 => "Already Reported",
        226 => "IM Used",
        300 => "Multiple Choices",
        301 => "Moved Permanently",
        302 => "Found",
        303 => "See Other",
        304 => "Not Modified",
        305 => "Use Proxy",
        307 => "Temporary Redirect",
        308 => "Permanent Redirect",
        400 => "Bad Request",
        401 => "Unauthorized",
        402 => "Payment Required",
        403 => "Forbidden",
        404 => "Not Found",
        405 => "Method Not Allowed",
        406 => "Not Acceptable",
        407 => "Proxy Authentication Required",
        408 => "Request Timeout",
        409 => "Conflict",
        410 => "Gone",
        411 => "Length Required",
        412 => "Precondition Failed",
        413 => "Payload Too Large",
        414 => "URI Too Long",
        415 => "Unsupported Media Type",
        416 => "Range Not Satisfiable",
        417 => "Expectation Failed",
        421 => "Misdirected Request",
        422 => "Unprocessable Entity",
        423 => "Locked",
        424 => "Failed Dependency",
        426 => "Upgrade Required",
        428 => "Precondition Required",
        429 => "Too Many Requests",
        431 => "Request Header Fields Too Large",
        451 => "Unavailable For Legal Reasons",
        500 => "Internal Server Error",
        501 => "Not Implemented",
        502 => "Bad Gateway",
        503 => "Service Unavailable",
        504 => "Gateway Timeout",
        505 => "HTTP Version Not Supported",
        506 => "Variant Also Negotiates",
        507 => "Insufficient Storage",
        508 => "Loop Detected",
        510 => "Not Extended",
        511 => "Network Authentication Required"
    ];

    /**
     * HTTP status code
     *
     * @var int
     */
    private $code;

    /**
     * 
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
        if(!isset(self::REASON_PHRASE[$code])){
            throw new \InvalidArgumentException();
        }if($body !== null && (!$body->isWritable() || !$body->isReadable())){
            throw new \InvalidArgumentException();
        }

        $this->code     = $code;
        $this->phrase   = self::REASON_PHRASE[$code] ?? null;

        parent::__construct($headers, $body ?? Stream::fromPath("php://memory", "w"));
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
        if(!is_int($code) || isset(self::REASON_PHRASE[$code])){
            throw new \InvalidArgumentException();
        }else if($reasonPhrase !== "" && self::REASON_PHRASE[$code] !== $reasonPhrase){
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