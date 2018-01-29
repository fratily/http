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
class EmptyResponse implements Response{
    
    /**
     * Constructor
     * 
     * @param   int $code
     */
    public function __construct(int $code = 200){
        parent::__construct($code, [], new Stream\MemoryStream("r"));
    }
}