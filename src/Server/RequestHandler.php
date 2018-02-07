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

/**
 * 
 */
class RequestHandler implements RequestHandlerInterface{
    
    /**
     * @var \SplQueue
     */
    private $queue;
    
    /**
     * @var bool[]
     */
    private $classes;
    
    /**
     * @var ResponseInterface|null
     */
    private $response;
    
    /**
     * @var bool
     */
    private $ran    = false;
    
    /**
     * Constructor
     */
    public function __construct(ResponseInterface $response = null){
        $this->queue    = new \SplQueue();
        $this->classes  = [];
        $this->response = $response ?? new \Fratily\Http\Message\Response();
    }
    
    /**
     * レスポンスインスタンスをセット
     * 
     * @param   ResponseInterface   $response
     * 
     * @return  void
     */
    public function setResponse(ResponseInterface $response){
        $this->response = $response;
    }
    
    /**
     * {@inheritdoc}
     * 
     * @param   ServerRequestInterface  $request
     * 
     * @return  ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface{
        $this->ran  = true;
        
        if($this->queue->isEmpty()){
            if($this->response === null){
                throw new \RuntimeException;
            }
            
            return $this->response;
        }
        
        return $this->queue->dequeue()->process($request, $this);
    }
    
    /**
     * ミドルウェアを末尾に追加する
     * 
     * @param   MiddlewareInterface $middleware
     * 
     * @throws  \RuntimeException
     */
    public function append(MiddlewareInterface $middleware){
        if($this->ran){
            throw new \RuntimeException;
        }
        
        $this->queue->push($middleware);
        $this->addClass($middleware);
        
        return $this;
    }

    /**
     * ミドルウェアを先頭に追加する
     *
     * @param   MiddlewareInterface $middleware
     *
     * @return  $this
     * 
     * @throws  \RuntimeException
     */
    public function prepend(MiddlewareInterface $middleware){
        if($this->ran){
            throw new \RuntimeException;
        }
        
        $this->queue->unshift($middleware);
        $this->addClass($middleware);
        
        return $this;
    }

    /**
     * 指定したミドルウェアクラスの前にミドルウェアを挿入する
     *
     * @param   string  $name
     * @param   MiddlewareInterface $middleware
     *
     * @return  $this
     * 
     * @throws  \RuntimeException
     */
    public function insertBefore(string $name, MiddlewareInterface $middleware){
        if($this->ran){
            throw new \RuntimeException;
        }
        
        if($this->hasClass($name)){
            $keys   = [];
            $i      = 0;
            
            foreach($this->queue as $key => $val){
                if($name === get_class($val)){
                    $keys[] = $key;
                }
            }
            
            foreach($keys as $key){
                $this->queue->add($key + $i++, $middleware);
            }
            
            $this->addClass($middleware);
        }else{
            throw new \RuntimeException;
        }

        return $this;
    }

    /**
     * 指定したミドルウェアクラスの後にミドルウェアを挿入する
     *
     * @param   string $name
     * @param   MiddlewareInterface $middleware
     *
     * @return  $this
     * 
     * @throws  \RuntimeException
     */
    public function insertAfter(string $name, MiddlewareInterface $middleware){
        if($this->ran){
            throw new \RuntimeException;
        }
        
        if($this->hasClass($name)){
            $keys   = [];
            $i      = 1;
            
            foreach($this->queue as $key => $val){
                if($name === get_class($val)){
                    $keys[] = $key;
                }
            }
            
            foreach($keys as $key){
                $this->queue->add($key + $i++, $middleware);
            }
            
            $this->addClass($middleware);
        }else{
            $this->append($middleware);
        }

        return $this;
    }
    
    /**
     * 指定したミドルウェアオブジェクトの前にミドルウェアを挿入する
     *
     * @param   MiddlewareInterface $target
     * @param   MiddlewareInterface $middleware
     *
     * @return  $this
     * 
     * @throws  \RuntimeException
     */
    public function insertBeforeObject(MiddlewareInterface $target, MiddlewareInterface $middleware){
        if($this->ran){
            throw new \RuntimeException;
        }
        
        $keys   = [];
        $i      = 0;

        foreach($this->queue as $key => $val){
            if($val === $target){
                $keys[] = $key;
            }
        }

        if(empty($keys)){
            throw new \RuntimeException;
        }
        
        foreach($keys as $key){
            $this->queue->add($key + $i++, $middleware);
        }

        $this->addClass($middleware);
        
        return $this;
    }

    /**
     * 指定したミドルウェアオブジェクトの後にミドルウェアを挿入する
     *
     * @param   MiddlewareInterface $target
     * @param   MiddlewareInterface $middleware
     *
     * @return  $this
     * 
     * @throws  \RuntimeException
     */
    public function insertAfterObject(MiddlewareInterface $target, MiddlewareInterface $middleware){
        if($this->ran){
            throw new \RuntimeException;
        }

        $keys   = [];
        $i      = 1;

        foreach($this->queue as $key => $val){
            if($val === $target){
                $keys[] = $key;
            }
        }

        if(empty($keys)){
            foreach($keys as $key){
                $this->queue->add($key + $i++, $middleware);
            }
        }else{
            $this->append($middleware);
        }

        $this->addClass($middleware);

        return $this;
    }

    
    /**
     * 指定した位置にミドルウェアを挿入する
     * 
     * @param   int $key
     * @param   MiddlewareInterface $middleware
     *
     * @return  $this
     * 
     * @throws  \RuntimeException
     */
    public function insertAt(int $key, MiddlewareInterface $middleware){
        if($this->ran){
            throw new \RuntimeException;
        }
        
        if(isset($this->queue[$key])){
            $this->queue->add($key, $middleware);
            $this->addClass($middleware);
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
     * ミドルウェア登録フラグを立てる
     * 
     * @param   MiddlewareInterafce $middleware
     * 
     * @return  void
     */
    private function addClass(MiddlewareInterface $middleware){
        $this->classes[get_class($middleware)]  = true;
    }
    
    /**
     * 指定した名前のミドルウェアが登録されているか確認する
     * 
     * @param   string  $name
     * 
     * @return  bool
     */
    public function hasClass(string $name){
        return $this->classes[$name] ?? false;
    }
}