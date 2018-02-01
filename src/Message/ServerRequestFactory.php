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

use Interop\Http\Factory\ServerRequestFactoryInterface;
use Interop\Http\Factory\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

/**
 *
 */
class ServerRequestFactory implements ServerRequestFactoryInterface{
    
    /**
     * {@inheritdoc}
     */
    public function createServerRequest($method, $uri){
        if(!is_string($method)){
            throw new \InvalidArgumentException();
        }else if(!is_string($uri) && !($uri instanceof UriInterface)){
            throw new \InvalidArgumentException();
        }
        
        $uri        = is_string($uri) ? new Uri($uri) : $uri;
        $body       = new Stream\InputStream();
        $parsedBody = null;
        
        if(in_array($method, ["POST", "PUT", "DELETE"])
            && ($_SERVER["CONTENT_TYPE"] ?? "") !== "application/x-www-form-urlencoded"
        ){
            $parsedBody = mb_parse_str($body->getContents());
            $body->rewind();
        }
        
        return new ServerRequest(
            $method,
            $uri,
            self::getRequestHeaders($_SERVER),
            $body,
            $_SERVER,
            self::getUploadedFiles($_FILES),
            $_COOKIE,
            $_GET,
            $parsedBody,
            self::getProtocolVersion($_SERVER)
        );
    }

    /**
     * Create a new server request from server variables.
     *
     * @param array $server Typically $_SERVER or similar structure.
     *
     * @return ServerRequestInterface
     *
     * @throws \InvalidArgumentException
     *  If no valid method or URI can be determined.
     */
    public function createServerRequestFromArray(array $server){
        $method     = $server["REQUEST_METHOD"] ?? "GET";
        $uri        = self::resolveUri($server);
        $body       = new Stream\InputStream();
        $parsedBody = null;
        
        if(in_array($method, ["POST", "PUT", "DELETE"])
            && ($server["CONTENT_TYPE"] ?? "") === "application/x-www-form-urlencoded"
        ){
            $parsedBody = mb_parse_str($body->getContents());
            $body->rewind();
        }
        
        return new ServerRequest(
            $method,
            $uri,
            self::getRequestHeaders($server),
            $body,
            $server,
            self::getUploadedFiles($_FILES),
            $_COOKIE,
            $_GET,
            $parsedBody,
            self::getProtocolVersion($server)
        );
    }
    
    public static function getRequestHeaders(array $server = null){
        $return = [];

        foreach(($server ?? $_SERVER) as $key => $value){
            if((bool)preg_match("/\AHTTP_[0-9A-Z!#$%&'*+\-.^_`|~]+\z/", $key)){
                $key    = substr($key, 5);

                if(strlen($key) > 0){
                    $key    = implode(
                        "-", array_map("ucfirst", explode("_", strtolower($key)))
                    );

                    $return[$key]   = $value;
                }
            }
        }

        return $return;
    }
    
    /**
     * 
     * 
     * @param   mixed[] $files
     * @param   UploadedFileFactoryInterface    $factory
     * 
     * @return  UploadedFileInterface
     * 
     * @throws  \InvalidArgumentException
     */
    public static function getUploadedFiles(
        array $files = null,
        UploadedFileFactoryInterface $factory = null
    ){
        $return = [];
        
        foreach($files as $name => $value){
            if($value instanceof UploadedFileInterface){
                $return[$name]  = $value;
            }else if(is_array($value)){
                if(isset($value["error"]) && isset($value["tmp_name"])){
                    $return[$name]  = static::createUplodFile($value, $factory);
                }else{
                    $return[$name]  = static::getUploadedFiles($value, $factory);
                }
            }else{
                throw new \InvalidArgumentException();
            }
        }
        
        return $return;
    }
    
    public static function getProtocolVersion($server = null){
        $server = $server ?? $_SERVER;
        
        if(!isset($server["SERVER_PROTOCOL"])){
            return "1.1";
        }else if((bool)preg_match(
                "`\AHTTP/(?<ver>[1-9][0-9]*(\.[1-9][0-9]*)?)\z`",
                $server["SERVER_PROTOCOL"], $m
            )
        ){
            throw new \InvalidArgumentException();
        }
        
        return $m["ver"];
    }
    
    private static function resolveUri(){
        
    }
    
    /**
     * 
     * 
     * @param   mixed[] $value
     * @param   UploadedFileFactoryInterface    $factory
     * 
     * @return  UploadedFileInterface
     */
    private static function createUplodFile(
        array $value,
        UploadedFileFactoryInterface $factory = null
    ){
        if(is_array($value["error"])){
            return self::createUploadNestFile($value, $factory);
        }else if($factory !== null){
            return $factory->createUploadedFile(
                $value["tmp_name"],
                $value["size"],
                $value["error"],
                $value["name"],
                $value["size"]
            );
        }
        
        return new UploadedFile(
            $value["tmp_name"],
            $value["size"],
            $value["error"],
            $value["name"],
            $value["size"]
        );
    }
    
    /**
     * 
     * 
     * @param   mixed[] $files
     * @param   UploadedFileFactoryInterface    $factory
     * 
     * @return  UploadedFileInterface[]
     */
    private static function createUploadNestFile(
        array $files,
        UploadedFileFactoryInterface $factory = null
    ){
        $return = [];
        
        foreach(array_keys($files["error"]) as $key){
            $info   = [
                "tmp_name"  => $files["tmp_name"][$key],
                "size"      => $files["size"][$key],
                "error"     => $files["error"][$key],
                "name"      => $files["name"][$key],
                "type"      => $files["type"][$key],
            ];
            
            $return[$key]   = self::createUplodFile($info, $factory);
        }
        
        return $return;
    }
}