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
namespace Fratily\Http\Message\Response;

use Fratily\Http\Message\Response;
use Fratily\Http\Message\Stream;

/**
 * 
 */
class JsonResponse implements Response{
    
    /**
     * Constructor
     * 
     * @param   array   $data
     * @param   int $code
     */
    public function __construct(array $data = [], int $code = 200){
        $stream = new Stream\MemoryStream();
        
        json_encode(null);
        $json   = json_encode($data);
        
        if(json_last_error() !== JSON_ERROR_NONE){
            throw new \RuntimeException(
                "Json encode error: " . json_last_error_msg()
            );
        }
        
        $stream->write($json);
        
        parent::__construct($code, ["Content-Type" => "application/json"], $stream);
    }
}