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
namespace Fratily\Http\Message\Response;

use Psr\Http\Message\ResponseInterface;

/**
 * 
 */
class Emitter implements EmitterInterface{
    
    /**
     * {@inheritdoc}
     * 
     * @param   int $bufferSize
     * 
     * @throws  \RuntimeException
     */
    public function emit(ResponseInterface $response, int $bufferSize = 4096){
        while(ob_get_level() > 0){
            ob_end_flush();
        }
        
        if(!headers_sent($file, $line)){
            $this->emitHttpStatus($response);
            $this->emitHeaders($response);
        }
        
        if($response->getBody()->getSize() > 0){
            $this->emitBody($response, $bufferSize);
        }
    }
    
    /**
     * HTTPステータスヘッダを送信する
     * 
     * @param   ResponseInterface   $response
     * 
     * @return  void
     */
    protected function emitHttpStatus(ResponseInterface $response){
        $phrese = $response->getReasonPhrase();
        
        header(sprintf("HTTP/%s %d%s",
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $phrese === "" ? "" : " {$phrese}"
        ));
    }
    
    /**
     * ヘッダーを送信する
     * 
     * @param   ResponseInterface   $response
     * 
     * @return  void
     */
    protected function emitHeaders(ResponseInterface $response){
        foreach($response->getHeaders() as $name => $values){
            if(strtolower($name) === "set-cookie"){
                foreach($values as $value){
                    header(sprintf("Set-Cookie: %s", $value), false);
                }
            }else{
                header(sprintf("%s: %s", $name, implode(", ", $values)));
            }
        }
    }
    
    /**
     * メッセージボディを送信する
     * 
     * @param   ResponseInterface   $response
     * @param   int $bufferSize
     * 
     * @return  void
     * 
     * @throws  \InvalidArgumentException
     */
    protected function emitBody(ResponseInterface $response, int $bufferSize){
        if($bufferSize < 1){
            throw new \InvalidArgumentException();
        }
        //  NO Content と Not Modified はボディが必要ない
        if(in_array($response->getStatusCode(), [204, 304])){
            return;
        }
        
        $body   = $response->getBody();

        //  シークできるならバッファサイズに合わせてリードする
        if($body->isSeekable()){
            $body->rewind();
            while(!$body->eof()){
                echo $body->read($bufferSize);
            }
        }else{
            echo $body->getContents();
        }
    }
}