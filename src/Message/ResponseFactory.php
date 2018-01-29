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

use Interop\Http\Factory\ResponseFactoryInterface;

/**
 *
 */
class ResponseFactory implements ResponseFactoryInterface{
    
    /**
     * {@inheritdoc}
     */
    public function createResponse($code = 200){
        return new Response($code);
    }
}