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
namespace Fratily\Http\Message\Exception;

class UnreadableException extends \RuntimeException implements HttpException{
    
    public function __construct(string $msg = null, int $code = 0, \Throwable $prev = null){
        $msg    = $msg ?? "Cant read the stream";
        
        parent::__construct($msg, $code, $prev);
    }
}