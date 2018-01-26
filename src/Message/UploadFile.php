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
    
    const ERROR_MAP = [
        UPLOAD_ERR_OK         => true,
        UPLOAD_ERR_INI_SIZE   => false,
        UPLOAD_ERR_FORM_SIZE  => false,
        UPLOAD_ERR_PARTIAL    => false,
        UPLOAD_ERR_NO_FILE    => false,
        UPLOAD_ERR_NO_TMP_DIR => false,
        UPLOAD_ERR_CANT_WRITE => false,
        UPLOAD_ERR_EXTENSION  => false,
    ];
    
    /**
     * @var string
     */
    private $temp;
    
    /**
     * @var StreamInterface|null
     */
    private $stream;
    
    /**
     * @var string
     */
    private $clientFilename;
    
    /**
     * @var string
     */
    private $clientMediaType;
    
    /**
     * @var int
     */
    private $size;
    
    /**
     * @var mixed
     */
    private $error;
    
    /**
     * @var bool
     */
    private $moved  = false;
    
    /**
     * nameからUploadFileを作成する。
     * 
     * @param   string  $name
     * 
     * @return  static|static[]
     * 
     * @deprecated  これはファクトリーの仕事なのでいずれ分割する
     */
    public function fromName(string $name){
        if(!array_key_exists($name, $_FILES)){
            throw new \InvalidArgumentException();
        }
        
        $file   = $_FILES[$name];
        
        if(is_array($file["error"])){
            $return = [];
            
            foreach(array_keys($file["error"]) as $key){
                $return[]   = new static(
                    $file["error"][$key] ?? UPLOAD_ERR_EXTENSION,
                    $file["name"][$key] ?? null,
                    $file["type"][$key] ?? null,
                    $file["tmp_name"][$key] ?? null,
                    $file["size"][$key] ?? 0
                );
            }
        }else{
            $return = new static(
                $file["error"] ?? UPLOAD_ERR_EXTENSION,
                $file["name"] ?? null,
                $file["type"] ?? null,
                $file["tmp_name"] ?? null,
                $file["size"] ?? 0
            );
        }
        
        return $return;
    }
    
    /**
     * Constructor
     * 
     * @param   string  $file
     * @param   string  $clientFilename
     * @param   string  $clientMediaType
     * @param   int $size
     * @param   int $error
     * 
     * @throws  \InvalidArgumentException
     */
    public function __construct(
        $file,
        $size = null,
        $error = UPLOAD_ERR_OK,
        $clientFilename = null,
        $clientMediaType = null
    ){
        if(!isset(self::ERROR_MAP[$error])){
            throw new \InvalidArgumentException();
        }
        
        if($error === UPLOAD_ERR_OK){
            if(!is_file($file)){
                throw new \InvalidArgumentException();
            }
            
            $this->temp = $file;
        }
        
        $this->size             = $size;
        $this->error            = $error;
        $this->clientFilename   = $clientFilename;
        $this->clientMediaType  = $clientMediaType;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getStream(){
        if($this->stream === null){
            if($this->error !== UPLOAD_ERR_OK){
                throw new \RuntimeException;
            }else if($this->moved){
                throw new \RuntimeException;
            }
            
            $this->stream   = Stream::fromPath($this->temp);
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
            throw new \RuntimeException;
        }else if($this->moved){
            throw new \RuntimeException;
        }
        
        $dir    = dirname($targetPath);
        
        if(!is_dir($dir)){
            throw new \RuntimeException;
        }else if(!is_writable($dir)){
            throw new \RuntimeException;
        }
        
        if(move_uploaded_file($this->temp, $targetPath) === false){
            throw new \RuntimeException;
        }
        
        $this->moved    = true;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize(){
        return $this->size;
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