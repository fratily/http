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
    private $clientName;
    
    /**
     * @var string
     */
    private $clientType;
    
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
     * 
     * @param   string  $name
     *      $_FILES index key.
     * 
     * @return  static
     * 
     * @throws  \RuntimeException
     */
    public function fromName(string $name){
        if(!isset($_FILES[$name])){
            throw new \RuntimeException;
        }
        
        return new static(
            $_FILES[$name]["tmp_name"] ?? "",
            $_FILES[$name]["name"] ?? "",
            $_FILES[$name]["type"] ?? "",
            $_FILES[$name]["size"] ?? 0,
            $_FILES[$name]["error"] ?? -1
        );
    }
    
    /**
     * Constructor
     * 
     * @param   string  $temp
     * @param   string  $name
     * @param   string  $type
     * @param   int $size
     * @param   int $error
     * 
     * @throws  \InvalidArgumentException
     */
    public function __construct(
        string $temp,
        string $name,
        string $type,
        int $size,
        int $error
    ){
        if(!isset(self::ERROR_MAP[$error])){
            throw new \InvalidArgumentException();
        }
        
        if($error === UPLOAD_ERR_OK){
            if(!is_file($temp)){
                throw new \InvalidArgumentException();
            }
            
            $this->temp = $temp;
        }
        
        $this->clientName   = $name;
        $this->clientType   = $type;
        $this->size         = $size;
        $this->error        = $error;
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
        return $this->clientName;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientMediaType(){
        return $this->clientType;
    }
}