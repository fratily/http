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
namespace Fratily\Http\Message\Stream;

use Fratily\Http\Message\Stream;

/**
 *
 */
class MemoryStream extends Stream{

    /**
     * Constructor
     *
     * @throws  \InvalidArgumentException
     */
    public function __construct(string $mode = "wb+"){
        if(($resource = fopen("php://memory", $mode)) === false){
            throw new \RuntimeException;
        }

        parent::__construct($resource);
    }
}