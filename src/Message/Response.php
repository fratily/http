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
        if(!isset(HttpStatus::STATUS_PHRASES[$code])){
            throw new \InvalidArgumentException();
        }if($body !== null && (!$body->isWritable() || !$body->isReadable())){
            throw new \InvalidArgumentException();
        }

        $this->code     = $code;
        $this->phrase   = HttpStatus::STATUS_PHRASES[$code] ?? null;

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
        if(!is_int($code) || !isset(HttpStatus::STATUS_PHRASES[$code])){
            throw new \InvalidArgumentException();
        }else if($reasonPhrase !== "" && HttpStatus::STATUS_PHRASES[$code] !== $reasonPhrase){
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
}