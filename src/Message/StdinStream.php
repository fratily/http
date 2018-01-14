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

/**
 *
 */
class StdinStream extends Stream{

    private $contents;
    
    /**
     * Constructor
     *
     * @param   resource    $resource
     *
     * @throws  \InvalidArgumentException
     * @throws  Exception\InvalidStreamReferencedException
     */
    public function __construct(){
        parent::__construct(STDIN);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(){
        if($this->contents === null){
            $this->contents = parent::__toString();
        }
        
        return $this->contents;
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable(){
        return false;
    }
}