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

use Interop\Http\Factory\RequestFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 *
 */
class RequestFactory implements RequestFactoryInterface{

    /**
     * {@inheritdoc}
     *
     * @throws  \InvalidArgumentException
     */
    public function createRequest($method, $uri){
        if(!is_string($method)){
            throw new \InvalidArgumentException();
        }else if(!is_string($uri) && !($uri instanceof UriInterface)){
            throw new \InvalidArgumentException();
        }

        if(is_string($uri)){
            $uri    = new Uri($uri);
        }

        return new Request($method, $uri);
    }
}