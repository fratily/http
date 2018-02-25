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
namespace Fratily\Http\Server;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Interop\Http\Factory\ResponseFactoryInterface;

/**
 *
 */
class RequestHandler implements RequestHandlerInterface{

    /**
     * @var \SplQueue
     */
    private $queue;

    /**
     * @var \SplQueue|null
     */
    private $runningQueue;

    /**
     * @var int
     */
    private $runningLevel;

    /**
     * @var ResponseFactoryInterface|null
     */
    private $factory;

    /**
     * Constructor
     *
     * @param   ResponseFactoryInterface
     */
    public function __construct(ResponseFactoryInterface $factory){
        $this->runningLevel = 0;
        $this->queue        = new \SplQueue();
        $this->factory      = $factory;
    }

    /**
     * Clone
     */
    public function __clone(){
        $this->queue    = clone $this->queue;

        if($this->runningQueue !== null){
            $this->runningQueue = clone $this->runningQueue;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param   ServerRequestInterface  $request
     *
     * @return  ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface{
        if($this->runningQueue === null){
            $this->runningQueue = clone $this->queue;
        }

        $this->runningLevel++;

        if($this->runningQueue->isEmpty()){
            $response   = $this->factory->createResponse();
        }else{
            $response   = $this->runningQueue->dequeue()->process($request, $this);
        }

        $this->runningLevel--;

        if($this->runningLevel === 0){
            $this->runningQueue = null;
        }

        return $response;
    }

    /**
     * ミドルウェアを末尾に追加する
     *
     * @param   MiddlewareInterface $middleware
     */
    public function append(MiddlewareInterface $middleware){
        $this->queue->push($middleware);

        return $this;
    }

    /**
     * ミドルウェアを先頭に追加する
     *
     * @param   MiddlewareInterface $middleware
     *
     * @return  $this
     */
    public function prepend(MiddlewareInterface $middleware){
        $this->queue->unshift($middleware);

        return $this;
    }

    /**
     * 指定したミドルウェアクラスの前にミドルウェアを挿入する
     *
     * 指定クラスが存在しなければ例外をスローする。
     *
     * @param   string  $target
     * @param   MiddlewareInterface $middleware
     *
     * @return  $this
     *
     * @throws  \RuntimeException
     */
    public function insertBefore(string $target, MiddlewareInterface $middleware){
        if(($keys = $this->getClassIndexes($target)) !== null){
            $i  = 0;

            foreach($keys as $key){
                $this->queue->add($key + $i++, $middleware);
            }
        }else{
            throw new \RuntimeException;
        }

        return $this;
    }

    /**
     * 指定したミドルウェアクラスの後にミドルウェアを挿入する
     *
     * 指定クラスが存在しなければ末尾に追加する。
     *
     * @param   string  $target
     * @param   MiddlewareInterface $middleware
     *
     * @return  $this
     */
    public function insertAfter(string $target, MiddlewareInterface $middleware){
        if(($keys = $this->getClassIndexes($target)) !== null){
            $i  = 1;

            foreach($keys as $key){
                $this->queue->add($key + $i++, $middleware);
            }
        }else{
            $this->append($middleware);
        }

        return $this;
    }

    /**
     * 指定したミドルウェアオブジェクトの前にミドルウェアを挿入する
     *
     * 指定オブジェクトが存在しなければ例外をスローする。
     *
     * @param   MiddlewareInterface $target
     * @param   MiddlewareInterface $middleware
     *
     * @return  $this
     *
     * @throws  \RuntimeException
     */
    public function insertBeforeObject(MiddlewareInterface $target, MiddlewareInterface $middleware){
        if(($keys = $this->getObjectIndexes($target)) !== null){
            $i  = 0;

            foreach($keys as $key){
                $this->queue->add($key + $i++, $middleware);
            }
        }else{
            throw new \RuntimeException;
        }

        return $this;
    }

    /**
     * 指定したミドルウェアオブジェクトの後にミドルウェアを挿入する
     *
     * 指定オブジェクトが存在しなければ末尾に追加する。
     *
     * @param   MiddlewareInterface $target
     * @param   MiddlewareInterface $middleware
     *
     * @return  $this
     */
    public function insertAfterObject(MiddlewareInterface $target, MiddlewareInterface $middleware){
        if(($keys = $this->getObjectIndexes($target)) !== null){
            $i  = 1;

            foreach($keys as $key){
                $this->queue->add($key + $i++, $middleware);
            }
        }else{
            $this->append($middleware);
        }

        return $this;
    }


    /**
     * 指定した位置にミドルウェアを挿入する
     *
     * 指定位置が範囲場合の場合は末尾に追加される。
     *
     * @param   int $key
     * @param   MiddlewareInterface $middleware
     *
     * @return  $this
     */
    public function insertAt(int $key, MiddlewareInterface $middleware){
        if(isset($this->queue[$key])){
            $this->queue->add($key, $middleware);
        }else{
            $this->append($middleware);
        }

        return $this;
    }

    /**
     * 指定したミドルウェアクラスの位置を返す
     *
     * @param   string  $target
     *
     * @return  int[]|null
     */
    protected function getClassIndexes(string $target){
        $keys   = [];
        $find   = false;

        foreach($this->queue as $key => $val){
            if($val === $target){
                $find   = true;
                $keys[] = $key;
            }
        }

        return $find ? $keys : null;
    }

    /**
     * 指定したミドルウェアオブジェクトの位置を返す
     *
     * @param   MiddlewareInterface $target
     *
     * @return  int[]|null
     */
    protected function getObjectIndexes(MiddlewareInterface $target){
        $keys   = [];
        $find   = false;

        foreach($this->queue as $key => $val){
            if($val === $target){
                $find   = true;
                $keys[] = $key;
            }
        }

        return $find ? $keys : null;
    }

    /**
     * 指定した名前のミドルウェアが登録されているか確認する
     *
     * @param   string  $name
     *
     * @return  bool
     */
    public function hasClass(string $name){
        return $this->getClassIndexes($name) !== null;
    }
}