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

use Interop\Http\Factory\UriFactoryInterface;

/**
 *
 */
class UriFactory implements UriFactoryInterface{

    /**
     * {@inheritdoc}
     */
    public function createUri($uri = ""){
        if(!is_string($uri)){
            throw new \InvalidArgumentException();
        }

        return new Uri($uri);
    }
    
    /**
     * サーバー変数からURIを作成する
     * 
     * @param   mixed[] $server
     * 
     * @return  Uri
     */
    public function createFromServer(array $server = null){
        $server = $server ?? $_SERVER;
        
        $scheme     = $this->resolveScheme($server);
        $authority  = $this->resolveAuthority($server, $scheme);
        $path       = $this->resolvePathAndQuery($server);
        
        return new Uri("{$scheme}://{$authority}{$path}");
    }
    
    /**
     * サーバー変数からスキームを解決する
     * 
     * @param   mixed[] $server
     * 
     * @return  string
     */
    private static function resolveScheme(array $server){
        if(isset($server["REQUEST_SCHEME"]) && $server["REQUEST_SCHEME"] !== ""){
            $scheme = strtolower($server["REQUEST_SCHEME"]);
        }else if(isset($server["HTTPS"]) && $server["HTTPS"] !== "off"){
            $scheme = "https";
        }else{
            $scheme = "http";
        }
        
        return $scheme;
    }
    
    /**
     * サーバー変数からホストとポートを解決する
     * 
     * @param   mixed[] $server
     * @param   string  $scheme
     * 
     * @return  string
     */
    private static function resolveAuthority(array $server, string $scheme){
        if(isset($server["HTTP_HOST"]) && $server["HTTP_HOST"] !== ""){
            if((bool)preg_match("/:([1-9][0-9]*)\z/", $server["HTTP_HOST"], $m)){
                $host   = substr($server["HTTP_HOST"], 0, -1 - strlen($m[1]));
                $port   = (int)$m[1];
            }else{
                $host   = $server["HTTP_HOST"];
                $port   = null;
            }
        }else{
            if(isset($server["SERVER_NAME"]) && $server["SERVER_NAME"] !== ""){
                $host   = $server["SERVER_NAME"];
            }else if(isset($server["SERVER_ADDR"])){
                $host   = $server["SERVER_ADDR"];
                
                if(strpos($host, ":") !== false){
                    $host   = "[{$host}]";
                }
            }else{
                return "";
            }
            
            if(isset($server["SERVER_PORT"])){
                $port   = (int)$server["SERVER_PORT"];
            }
        }
        
        if($scheme === "http" && $port === 80
            || $scheme === "https" && $port === 443
        ){
            $port   = null;
        }
        
        return $host . ($port !== null ? ":" . $port : "");
    }
    
    /**
     * サーバー変数からパスを解決する
     * 
     * @param   mixed[] $server
     * 
     * @return  string
     */
    private static function resolvePathAndQuery(array $server){
        if(isset($server["REQUEST_URI"])){
            return $server["REQUEST_URI"];
        }
        
        return "/";
    }
}