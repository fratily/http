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
namespace Fratily\Http;

use Psr\Http\Message\UriInterface;

class Uri implements UriInterface{
    
    const REGEX = "(?<scheme>[a-z][0-9a-z-+.]*)://(?:(?:(?<userinfo>(?:%[0-9a-f"
        . "][0-9a-f]|[0-9a-z-._~!$&'()*+,;=:])*)@)?(?<host>\[(?:::(?:ffff:(?:[0"
        . "-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(?:\.(?:[0-9]|[1-9][0"
        . "-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}|(?:(?:[0-9a-f]|[1-9a-f][0-9"
        . "a-f]{1,3})(?::(?:[0-9a-f]|[1-9a-f][0-9a-f]{1,3})){0,5})?)|(?:[0-9a-f"
        . "]|[1-9a-f][0-9a-f]{1,3})(?:::(?:(?:[0-9a-f]|[1-9a-f][0-9a-f]{1,3})(?"
        . "::(?:[0-9a-f]|[1-9a-f][0-9a-f]{1,3})){0,4})?|:(?:[0-9a-f]|[1-9a-f][0"
        . "-9a-f]{1,3})(?:::(?:(?:[0-9a-f]|[1-9a-f][0-9a-f]{1,3})(?::(?:[0-9a-f"
        . "]|[1-9a-f][0-9a-f]{1,3})){0,3})?|:(?:[0-9a-f]|[1-9a-f][0-9a-f]{1,3})"
        . "(?:::(?:(?:[0-9a-f]|[1-9a-f][0-9a-f]{1,3})(?::(?:[0-9a-f]|[1-9a-f][0"
        . "-9a-f]{1,3})){0,2})?|:(?:[0-9a-f]|[1-9a-f][0-9a-f]{1,3})(?:::(?:(?:["
        . "0-9a-f]|[1-9a-f][0-9a-f]{1,3})(?::(?:[0-9a-f]|[1-9a-f][0-9a-f]{1,3})"
        . ")?)?|:(?:[0-9a-f]|[1-9a-f][0-9a-f]{1,3})(?:::(?:[0-9a-f]|[1-9a-f][0-"
        . "9a-f]{1,3})?|(?::(?:[0-9a-f]|[1-9a-f][0-9a-f]{1,3})){3})))))|v[0-9a-"
        . "f]\.(?:[0-9a-z-._~!$&'()*+,;=:])+)\]|(?:%[0-9a-f][0-9a-f]|[0-9a-z-._"
        . "~!$&'()*+,;=])*)(?::(?<port>[1-9][0-9]*))?)?(?<path>(?:/(?:%[0-9a-f]"
        . "[0-9a-f]|[0-9a-z-._~!$&'()*+,;=:@])*)*)(?:\?(?<query>(?:%[0-9a-f][0-"
        . "9a-f]|[0-9a-z-._~!$&'()*+,;=:@/?[\]])*))?(?:#(?<fragment>(?:%[0-9a-f"
        . "][0-9a-f]|[0-9a-z-._~!$&'()*+,;=:@/?])*))?";
    
    const REGEX_SCHEME      = "[a-z][0-9a-z-+.]*";
    
    const REGEX_USERINFO    = "(%[0-9a-f][0-9a-f]|[0-9a-z-._~!$&'()*+,;=:])*";
    
    const REGEX_HOST        = "(\[(::(ffff:([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4]"
        . "[0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5]))"
        . "{3}|(([0-9a-f]|[1-9a-f][0-9a-f]{1,3})(:([0-9a-f]|[1-9a-f][0-9a-f]{1,"
        . "3})){0,5})?)|([0-9a-f]|[1-9a-f][0-9a-f]{1,3})(::(([0-9a-f]|[1-9a-f]["
        . "0-9a-f]{1,3})(:([0-9a-f]|[1-9a-f][0-9a-f]{1,3})){0,4})?|:([0-9a-f]|["
        . "1-9a-f][0-9a-f]{1,3})(::(([0-9a-f]|[1-9a-f][0-9a-f]{1,3})(:([0-9a-f]"
        . "|[1-9a-f][0-9a-f]{1,3})){0,3})?|:([0-9a-f]|[1-9a-f][0-9a-f]{1,3})(::"
        . "(([0-9a-f]|[1-9a-f][0-9a-f]{1,3})(:([0-9a-f]|[1-9a-f][0-9a-f]{1,3}))"
        . "{0,2})?|:([0-9a-f]|[1-9a-f][0-9a-f]{1,3})(::(([0-9a-f]|[1-9a-f][0-9a"
        . "-f]{1,3})(:([0-9a-f]|[1-9a-f][0-9a-f]{1,3}))?)?|:([0-9a-f]|[1-9a-f]["
        . "0-9a-f]{1,3})(::([0-9a-f]|[1-9a-f][0-9a-f]{1,3})?|(:([0-9a-f]|[1-9a-"
        . "f][0-9a-f]{1,3})){3})))))|v[0-9a-f]\.([0-9a-z-._~!$&'()*+,;=:])+)\]|"
        . "(%[0-9a-f][0-9a-f]|[0-9a-z-._~!$&'()*+,;=])*)";
    
    const REGEX_PATH        = "(/(%[0-9a-f][0-9a-f]|[0-9a-z-._~!$&'()*+,;=:@])*)*";
    
    const REGEX_QUERY       = "(%[0-9a-f][0-9a-f]|[0-9a-z-._~!$&'()*+,;=:@/?[\]])*";
    
    const REGEX_FRAGMENT    = "(%[0-9a-f][0-9a-f]|[0-9a-z-._~!$&'()*+,;=:@/?])*";
    
    const SCHEME_PORT_MAP   = [
        "ftp"       => 21,
        "ssh"       => 22,
        "sftp"      => 22,
        "telnet"    => 23,
        "dns"       => 53,
        "http"      => 80,
        "pop"       => 110,
        "nfs"       => 111,
        "imap"      => 143,
        "ldap"      => 389,
        "https"     => 443,
        "smb"       => 445,
        "afp"       => 548,
        "ldaps"     => 636,
        "rsync"     => 873,
        "dict"      => 2628,
        "svn"       => 3690,
        "redis"     => 6379,
        "git"       => 9418
    ];
    
    const URLENCODE_RFC3986 = "rfc3986";
    
    const URLENCODE_FORM    = "application/x-www-form-urlencoded";
    
    /**
     * @var string
     */
    private $scheme;

    /**
     * @var string
     */
    private $userinfo;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $query;

    /**
     * @var string
     */
    private $fragment;
    
    public static function parseUri(string $uri){
        if(!(bool)preg_match("`\A".self::REGEX."\z`i", $uri, $m)){
            return false;
        }
        
        return array_filter($m, function($v, $k){
            return is_string($k) && $v !== "";
        }, ARRAY_FILTER_USE_BOTH);
    }
    
    public static function isStandardPort(string $scheme, int $port){
        if(!isset(static::SCHEME_PORT_MAP[strtolower($scheme)])){
            return false;
        }
        
        return static::SCHEME_PORT_MAP[strtolower($scheme)] === $port;
    }
    
    public static function urlEncode(string $str, $mode = self::URLENCODE_RFC3986){
        switch($mode){
            case self::URLENCODE_RFC3986:
                return rawurlencode($str);
                
            case self::URLENCODE_FORM:
                return urlencode($str);
        }
        
        throw new \InvalidArgumentException();
    }
    
    public static function urlDecode(string $str, $mode = self::URLENCODE_RFC3986){
        switch($mode){
            case self::URLENCODE_RFC3986:
                return rawurldecode($str);
                
            case self::URLENCODE_FORM:
                return urldecode($str);
        }
        
        throw new \InvalidArgumentException();
    }
    
    public function __construct(string $uri){
        if(($parts = self::parseUri($uri)) === false){
            throw new \InvalidArgumentException();
        }
        
        $this->scheme   = $parts["scheme"];
        $this->userinfo = $parts["userinfo"] ?? null;
        $this->host     = $parts["host"] ?? null;
        $this->port     = ($parts["port"] ?? null) !== null ? (int)$parts["port"] : null;
        $this->path     = $parts["path"] ?? "/";
        $this->query    = $parts["query"] ?? null;
        $this->fragment = $parts["fragment"] ?? null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function __toString(){
        return $this->scheme . "://"
            . $this->getAuthority()
            . $this->getPath()
            . ($this->query !== null) ? "?" . $this->query : ""
            . ($this->fragment !== null) ? "#" . $this->fragment : "";
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme(){
        return $this->scheme;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthority(){
        if($this->host === null){
            return "";
        }

        return
            isset($this->userinfo) ? $this->userinfo . "@" : ""
            . $this->host
            . isset($this->port) ? ":" . $this->port : "";
    }

    /**
     * {@inheritdoc}
     */
    public function getUserInfo(){
        return $this->userInfo ?? "";
    }
    
    /**
     * 
     * @return  string
     */
    public function getUser(){
        if($this->userinfo === null
            || ($pos = strpos($this->userinfo ?? "", ":")) === false
        ){
            return $this->userinfo ?? "";
        }
        
        return substr($this->userinfo, 0, $pos);
    }
    
    /**
     * 
     * @return  string
     */
    public function getPassword(){
        if($this->userinfo === null
            || ($pos = strpos($this->userinfo ?? "", ":")) === false
        ){
            return "";
        }
        
        return substr($this->userinfo, $pos + 1);
    }

    /**
     * {@inheritdoc}
     */
    public function getHost(){
        return $this->host ?? "";
    }

    /**
     * {@inheritdoc}
     */
    public function getPort(){
        if($this->port === null){
            return null;
        }
        
        return static::isStandardPort($this->getScheme(), $this->port)
            ? null : $this->port;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(){
        return $this->path ?? "/";
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery(){
        return $this->query ?? "";
    }
    
    /**
     * パースしたクエリ配列を返す
     * 
     * @return  mixed[]
     */
    public function getParsedQuery(){
        mb_parse_str($this->query ?? "", $return);
        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function getFragment(){
        return $this->fragment ?? "";
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function withScheme($scheme){
        if(!is_string($scheme)){
            throw new InvalidArgumentException();
        }else if(!(bool)preg_match("`\A".self::REGEX_SCHEME."\z`i", $scheme)){
            throw new InvalidArgumentException();
        }
        
        if($scheme === $this->scheme){
            $return = $this;
        }else{
            $return = clone $this;
            $return->scheme = $scheme;
        }
        
        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function withUserInfo($user, $password = null){
        if(!is_string($user)){
            throw new InvalidArgumentException();
        }else if($password !== null && !is_string($password)){
            throw new InvalidArgumentException();
        }
        
        if($user === ""){
            $userinfo   = null;
        }else{
            $userinfo   = self::urlEncode($user, self::URLENCODE_RFC3986);

            if($password !== null){
                $userinfo   .= ":" . self::urlEncode($password, self::URLENCODE_RFC3986);
            }
            
            if(!(bool)preg_match("`\A".self::REGEX_USERINFO."\z`i", $userinfo)){
                throw new \LogicException;
            }
        }
        
        if($userinfo === $this->userinfo){
            $return = $this;
        }else{
            $return = clone $this;
            $return->userinfo   = $userinfo;
        }
        
        return $userinfo;
    }

    /**
     * {@inheritdoc}
     */
    public function withHost($host){
        if(!is_string($host)){
            throw new \InvalidArgumentException();
        }else if(!(bool)preg_match("`\A".self::REGEX_HOST."\z`i", $host)){
            throw new \InvalidArgumentException();
        }
        
        if($host === $this->host){
            $return = $this;
        }else{
            $return = clone $this;
            $return->host   = $host;
        }
        
        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function withPort($port){
        if($port !== null && !is_int($port)){
            throw new \InvalidArgumentException();
        }else if(is_int($port) && ($port < 1 || 65535 < $port)){
            throw new \InvalidArgumentException();
        }
        
        if($port === $this->port){
            $return = $this;
        }else{
            $return = clone $this;
            $return->port   = $port;
        }
        
        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function withPath($path){
        if(!is_string($path)){
            throw new \InvalidArgumentException();
        }else if(!(bool)preg_match("`\A".self::REGEX_PATH."\z`i", $path)){
            throw new \InvalidArgumentException();
        }
        
        if($path === $this->path){
            $return = $this;
        }else{
            $return = clone $this;
            $return->path   = $path;
        }
        
        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function withQuery($query){
        if(!is_string($query)){
            throw new \InvalidArgumentException();
        }else if(!(bool)preg_match("`\A".self::REGEX_QUERY."\z`i", $query)){
            throw new \InvalidArgumentException();
        }

        if($query === $this->query){
            $return = $this;
        }else{
            $return = clone $this;
            $return->query  = $query;
        }
        
        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function withFragment($fragment){
        if(!is_string($fragment)){
            throw new \InvalidArgumentException();
        }else if(!(bool)preg_match("`\A".self::REGEX_FRAGMENT."\z`i", $fragment)){
            throw new \InvalidArgumentException();
        }

        if($fragment === $this->fragment){
            $return = $this;
        }else{
            $return = clone $this;
            $return->fragment   = $fragment;
        }
        
        return $return;
    }
}