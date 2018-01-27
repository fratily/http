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
use Fratily\Http\Message\Exception;

/**
 *
 */
class StdoutStream extends Stream{

    /**
     * Constructor
     *
     * @throws  \InvalidArgumentException
     */
    public function __construct(){
        parent::__construct(STDOUT);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(){
        return "";
    }

    /**
     * {@inheritdoc}
     */
    public function getSize(){
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @throws  Exception\StreamException
     */
    public function tell(){
        if($this->resource === null){
            throw Exception\StreamException::unavailable();
        }

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function eof(){
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable(){
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable(){
        if($this->resource !== null){
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable(){
        return false;
    }
}