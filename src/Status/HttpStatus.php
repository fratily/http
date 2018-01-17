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
namespace Fratily\Http\Status;

/**
 *
 */
class HttpStatus extends \Exception{

    const STATUS_PHRASES    = [
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

    const STATUS_CODE   = 500;

    /**
     * Constructor
     * 
     * @param   string  $msg
     * @param   int $code
     */
    public function __construct(string $msg = "", int $code = 0){
        parent::__construct($msg, $code);
    }
    
    /**
     * To string
     * 
     * @return  string
     */
    public function __toString(){
        return static::STATUS_CODE . " " .
            (self::STATUS_PHRASES[static::STATUS_CODE] ?? "Undefined");
    }

    /**
     * 
     * @return  int
     */
    public function getStatus(){
        return static::STATUS_CODE;
    }

    /**
     * 
     * @return  string
     */
    public function getPhrase(){
        return self::STATUS_PHRASES[static::STATUS_CODE] ?? "Undefined";
    }
}