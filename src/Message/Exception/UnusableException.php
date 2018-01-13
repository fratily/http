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

class UnusableException extends \RuntimeException implements HttpMessageException{
    
    public function __construct(string $msg = null, int $code = 0, \Throwable $prev = null){
        $msg    = $msg ?? "The stream is in an unusable state";
        
        parent::__construct($msg, $code, $prev);
    }
}