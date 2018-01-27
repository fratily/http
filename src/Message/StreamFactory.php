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

use Interop\Http\Factory\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 *
 */
class StreamFactory implements StreamFactoryInterface{

    /**
     * @var string
     */
    private $dir;

    /**
     * @var string
     */
    private $prefix;

    /**
     * Constructor
     *
     * @param   string|null  $dir
     *      Path of temporary directory.
     * @param   string  $prefix
     *
     * @throws  \InvalidArgumentException()
     */
    public function __construct(string $dir = null, string $prefix = ""){
        if($dir !== null && (is_file($dir) || !is_dir($dir))){
            throw new \InvalidArgumentException();
        }

        $this->dir      = realpath($dir ?? sys_get_temp_dir());
        $this->prefix   = $prefix;
    }

    /**
     * PHPへの入力(php://input)のストリームを作成する
     *
     * @return  StreamInterface
     */
    public function createInput(){
        return new Stream\InputStream();
    }

    /**
     * PHPの出力(php://input)のストリームを作成する
     *
     * @return  StreamInterface
     */
    public function createOutput(){
        return new Stream\OutputStream();
    }

    /**
     * 標準エラー出力のストリームを作成する
     *
     * @return  StreamInterface
     */
    public function createStderr(){
        return new Stream\StderrStream();
    }

    /**
     * 標準出力のストリームを作成する
     *
     * @return  StreamInterface
     */
    public function createStdout(){
        return new Stream\StdoutStream();
    }

    /**
     * メモリ上で操作する新しいストリームを作成する
     *
     * @param   string  $mode
     *
     * @return  StreamInterface
     */
    public function createMemory(string $mode = "wb+"){
        if(($resource = fopen("php://memory", $mode)) === false){
            throw new \RuntimeException;
        }

        return new $this->createStreamFromResource($resource);
    }

    /**
     * {@inheritdoc}
     *
     * @throws  \InvalidArgumentException
     * @throws  \RuntimeException
     */
    public function createStream($content = ""){
        if(!is_string($content)){
            throw new \InvalidArgumentException();
        }

        $path   = tempnam($this->dir, $this->prefix);

        if($path === false){
            throw new \RuntimeException;
        }

        $stream = $this->createStreamFromFile($path, "r");

        if($content !== ""){
            $stream->write($content);
            $stream->rewind();
        }

        return $stream;
    }

    /**
     * {@inheritdoc}
     *
     * @throws  \InvalidArgumentException
     * @throws  \RuntimeException
     */
    public function createStreamFromFile($filename, $mode = "r"){
        if(!is_string($filename) || !is_string($mode)){
            throw new \InvalidArgumentException();
        }

        if(($resource = fopen($filename, $mode)) === false){
            throw new \RuntimeException;
        }

        return $this->createStreamFromResource($resource);
    }

    /**
     * {@inheritdoc}
     *
     * @throws  \InvalidArgumentException
     */
    public function createStreamFromResource($resource){
        if(!is_resource($resource)){
            throw new \InvalidArgumentException();
        }

        return new Stream($resource);
    }
}