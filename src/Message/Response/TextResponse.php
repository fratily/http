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
class TextResponse implements Response{
    
    /**
     * Constructor
     * 
     * @param   string  $text
     * @param   int $code
     */
    public function __construct(string $text, int $code = 200){
        $stream = new Stream\MemoryStream();
        
        $stream->write($text);
        
        parent::__construct($code, ["Content-Type" => "text/plain"], $stream);
    }
}