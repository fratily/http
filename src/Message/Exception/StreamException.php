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

class StreamException extends \RuntimeException{

    public static function unavailable(){
        return new static("");
    }

    public static function unseekable(){
        return new static("");
    }

    public static function unreadable(){
        return new static("");
    }

    public static function unwritable(){
        return new static("");
    }
}