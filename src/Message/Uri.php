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

use Psr\Http\Message\UriInterface;

/**
 *
 */
class Uri implements UriInterface{

    const REGEX = "(?<scheme>https?)://(?:(?:(?<userinfo>(?:%[0-9a-f][0-9a-f]|["
        . "0-9a-z-._~!$&'()*+,;=:])*)@)?(?<host>\[(?:::(?:ffff:(?:[0-9]|[1-9][0"
        . "-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(?:\.(?:[0-9]|[1-9][0-9]|1[0-9]["
        . "0-9]|2[0-4][0-9]|25[0-5])){3}|(?:(?:[0-9a-f]|[1-9a-f][0-9a-f]{1,3})("
        . "?::(?:[0-9a-f]|[1-9a-f][0-9a-f]{1,3})){0,5})?)|(?:[0-9a-f]|[1-9a-f]["
        . "0-9a-f]{1,3})(?:::(?:(?:[0-9a-f]|[1-9a-f][0-9a-f]{1,3})(?::(?:[0-9a-"
        . "f]|[1-9a-f][0-9a-f]{1,3})){0,4})?|:(?:[0-9a-f]|[1-9a-f][0-9a-f]{1,3}"
        . ")(?:::(?:(?:[0-9a-f]|[1-9a-f][0-9a-f]{1,3})(?::(?:[0-9a-f]|[1-9a-f]["
        . "0-9a-f]{1,3})){0,3})?|:(?:[0-9a-f]|[1-9a-f][0-9a-f]{1,3})(?:::(?:(?:"
        . "[0-9a-f]|[1-9a-f][0-9a-f]{1,3})(?::(?:[0-9a-f]|[1-9a-f][0-9a-f]{1,3}"
        . ")){0,2})?|:(?:[0-9a-f]|[1-9a-f][0-9a-f]{1,3})(?:::(?:(?:[0-9a-f]|[1-"
        . "9a-f][0-9a-f]{1,3})(?::(?:[0-9a-f]|[1-9a-f][0-9a-f]{1,3}))?)?|:(?:[0"
        . "-9a-f]|[1-9a-f][0-9a-f]{1,3})(?:::(?:[0-9a-f]|[1-9a-f][0-9a-f]{1,3})"
        . "?|(?::(?:[0-9a-f]|[1-9a-f][0-9a-f]{1,3})){3})))))|v[0-9a-f]\.(?:[0-9"
        . "a-z-._~!$&'()*+,;=:])+)\]|(?:%[0-9a-f][0-9a-f]|[0-9a-z-._~!$&'()*+,;"
        . "=])+)(?::(?<port>[1-9][0-9]*))?)(?<path>(?:/(?:%[0-9a-f][0-9a-f]|[0-"
        . "9a-z-._~!$&'()*+,;=:@])*)*)(?:\?(?<query>(?:%[0-9a-f][0-9a-f]|[0-9a-"
        . "z-._~!$&'()*+,;=:@/?[\]])*))?(?:#(?<fragment>(?:%[0-9a-f][0-9a-f]|[0"
        . "-9a-z-._~!$&'()*+,;=:@/?])*))?";

    const REGEX_SCHEME      = "https?";

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
        "http"      => 80,
        "https"     => 443
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

    /**
     * URL文字列を構造ごとに分割する
     *
     * @param   string  $uri
     *
     * @return  string[]|bool
     *      URLとして正しければ構造ごとに格納した配列を返す。空文字列や
     *      存在しない部分はインデックスされない。URLとして正しくなければ
     *      <b>FALSE</b>を返す。
     */
    public static function parseUri(string $uri){
        if(!(bool)preg_match("`\A".self::REGEX."\z`i", $uri, $m)){
            return false;
        }

        return array_filter($m, function($v, $k){
            return is_string($k) && $v !== "";
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * ポートがスキームのデフォルトポートか判定する
     *
     * @param   string  $scheme
     * @param   int $port
     *
     * @return  bool
     */
    public static function isStandardPort(string $scheme, int $port){
        if(!isset(static::SCHEME_PORT_MAP[strtolower($scheme)])){
            return false;
        }

        return static::SCHEME_PORT_MAP[strtolower($scheme)] === $port;
    }

    /**
     * URLエンコードを行う
     *
     * @param   string  $str
     * @param   mixed   $mode
     *      URLエンコードのタイプ
     *
     * @return  string
     *
     * @throws  \InvalidArgumentException
     */
    public static function urlEncode(string $str, $mode = self::URLENCODE_RFC3986){
        switch($mode){
            case self::URLENCODE_RFC3986:
                return rawurlencode($str);

            case self::URLENCODE_FORM:
                return urlencode($str);
        }

        throw new \InvalidArgumentException();
    }

    /**
     * URLデコードを行う
     *
     * @param   string  $str
     * @param   mixed   $mode
     *      URLエンコードのタイプ
     *
     * @return  string
     *
     * @throws  \InvalidArgumentException
     */
    public static function urlDecode(string $str, $mode = self::URLENCODE_RFC3986){
        switch($mode){
            case self::URLENCODE_RFC3986:
                return rawurldecode($str);

            case self::URLENCODE_FORM:
                return urldecode($str);
        }

        throw new \InvalidArgumentException();
    }

    /**
     * Cconstructor
     *
     * @param   string  $uri
     *
     * @throws  \InvalidArgumentException
     */
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
        $authority  = $this->getAuthority();

        return $this->scheme . ":"
            . ($authority !== "" ? "//{$authority}" : "")
            . $this->getPath()
            . ($this->query !== null ? "?" . $this->query : "")
            . ($this->fragment !== null ? "#" . $this->fragment : "");
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
        $userinfo   = $this->getUserInfo();
        $host       = $this->getHost();
        $port       = $this->getPort();

        return ($userinfo !== "" ? "{$userinfo}@" : "") . $host
            . ($port !== null ? ":{$port}" : "");
    }

    /**
     * {@inheritdoc}
     */
    public function getUserInfo(){
        return $this->userinfo ?? "";
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
     *
     * @throws  \InvalidArgumentException
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

        return $return;
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
     *
     * @throws  \InvalidArgumentException
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