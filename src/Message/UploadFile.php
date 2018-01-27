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

use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\StreamInterface;

/**
 *
 */
class UploadFile implements UploadedFileInterface{

    /**
     * @var string
     */
    private $file;

    /**
     * @var StreamInterface|null
     */
    private $stream;

    /**
     * @var int
     */
    private $size;

    /**
     * @var mixed
     */
    private $error;

    /**
     * @var string
     */
    private $clientFilename;

    /**
     * @var string
     */
    private $clientMediaType;

    /**
     * @var bool
     */
    private $moved  = false;

    /**
     * Constructor
     *
     * @param   string|resource|StreamInterface  $file
     * @param   int $size
     * @param   int $error
     * @param   string  $clientFilename
     * @param   string  $clientMediaType
     *
     * @throws  \InvalidArgumentException
     */
    public function __construct(
        $file,
        int $size = null,
        int $error = UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ){
        if(!isset(Exception\UploadFileException::ERROR_MAP[$error])){
            throw new \InvalidArgumentException();
        }

        if($error === UPLOAD_ERR_OK){
            if(is_string($file) && is_file($file)){
                $this->file = $file;
            }else if(is_resource($file)){
                $this->stream   = new Stream($file);
            }else if($file instanceof StreamInterface){
                $this->stream   = $file;
            }else{
                throw new \InvalidArgumentException();
            }
        }

        $this->size             = $size;
        $this->error            = $error;
        $this->clientFilename   = $clientFilename;
        $this->clientMediaType  = $clientMediaType;
    }

    /**
     * {@inheritdoc}
     *
     * @throws  Exception\UploadFileException
     */
    public function getStream(){
        if($this->error !== UPLOAD_ERR_OK){
            throw Exception\UploadFileException::uploadError($this->error);
        }else if($this->moved){
            throw Exception\UploadFileException::moved();
        }

        if($this->stream === null){

            if(($handle = fopen($this->file, "r")) === false){
                throw new \RuntimeException;
            }

            $this->stream   = new Stream($handle);
        }

        return $this->stream;
    }

    /**
     * {@inheritdoc}
     */
    public function moveTo($targetPath){
        if(!is_string($targetPath) || $targetPath === ""){
            throw new \InvalidArgumentException();
        }else if($this->error !== UPLOAD_ERR_OK){
            throw Exception\UploadFileException::uploadError($this->error);
        }else if($this->moved){
            throw Exception\UploadFileException::moved();
        }

        if($this->file !== null){
            if(is_uploaded_file($this->file)){
                if(move_uploaded_file($this->file, $targetPath) === false){
                    throw new \RuntimeException;
                }
            }else{
                if(rename($this->file, $targetPath) === false){
                    throw new \RuntimeException;
                }
            }
        }else{
            if(($handle = fopen($targetPath, "wb+"))){
                throw new \RuntimeException;
            }

            $stream = $this->getStream();

            $stream->rewind();

            while(!$stream->eof()){
                fwrite($handle, $stream->read(4096));
            }

            fclose($handle);
        }

        $this->moved    = true;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize(){
        return $this->size ?? $this->getStream()->getSize();
    }

    /**
     * {@inheritdoc}
     */
    public function getError(){
        return $this->error;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientFilename(){
        return $this->clientFilename;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientMediaType(){
        return $this->clientMediaType;
    }
}