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
class HtmlResponse implements Response{
    
    /**
     * Constructor
     * 
     * @param   string  $html
     * @param   int $code
     */
    public function __construct(string $html, int $code = 200){
        $stream = new Stream\MemoryStream();
        
        $stream->write($html);
        
        parent::__construct($code, ["Content-Type" => "text/html"], $stream);
    }
}