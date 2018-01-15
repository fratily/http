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
        
        $this->rewind();
        $this->contents = $this->getContents();
        $this->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(){
        return $this->contents;
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable(){
        return false;
    }
    
    public function getContents(){
        if($this->tell() === 0 && $this->contents !== null){
            return $this->contents;
        }
        
        return parent::getContents();
    }
}