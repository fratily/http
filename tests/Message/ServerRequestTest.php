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
namespace Fratily\HttpTest\Message;

use Fratily\Http\Message\ServerRequest;
use Fratily\Http\Message\Uri;

/**
 *
 */
class ServerRequestTest extends \PHPUnit\Framework\TestCase{
    
    /**
     * @dataProvider    provideGetServerParams
     */
    public function testGetServerParams($expected, ServerRequest $request){
        $this->assertEquals($expected, $request->getServerParams());
    }

    /**
     * @dataProvider    provideGetCookieParams
     */
    public function testGetCookieParams($expected, ServerRequest $request){
        $this->assertEquals($expected, $request->getCookieParams());
    }
    
    /**
     * @dataProvider    provideGetQueryParams
     */
    public function testGetQueryParams($expected, ServerRequest $request){
        $this->assertEquals($expected, $request->getQueryParams());
    }
    
    /**
     * @dataProvider    provideGetUploadedFiles
     */
    public function testGetUploadedFiles($expected, ServerRequest $request){
        $this->assertEquals($expected, $request->getUploadedFiles());
    }
    
    /**
     * @dataProvider    provideGetParsedBody
     */
    public function testGetParsedBody($expected, ServerRequest $request){
        $this->assertEquals($expected, $request->getParsedBody());
    }
    
    /**
     * @dataProvider    provideGetAttributes
     */
    public function testGetAttributes($expected, ServerRequest $request){
        $this->assertEquals($expected, $request->getAttributes());
    }
    
    /**
     * @dataProvider    provideGetAttribute
     */
    public function testGetAttribute($name, $default, $expected, ServerRequest $request){
        $this->assertEquals($expected, $request->getAttribute($name, $default));
    }
    
    public function testWith(){
        $this->assertEquals(true, true);
    }
    
    public function provideGetServerParams(){
        $method = "GET";
        $uri    = new Uri("http://example.com");
        
        return [
            [
                [
                    "HTTP_HOST" => "example.com"
                ],
                new ServerRequest($method, $uri, [
                    "HTTP_HOST" => "example.com"
                ])
            ],
        ];
    }
    
    public function provideGetCookieParams(){
        $method = "GET";
        $uri    = new Uri("http://example.com");
        
        return [
            [
                [
                    "foo"   => "v1",
                    "bar"   => "v2"
                ],
                new ServerRequest($method, $uri, [], [], 
                [
                    "foo"   => "v1",
                    "bar"   => "v2"
                ])
            ],
        ];
    }
    
    public function provideGetQueryParams(){
        $method = "GET";
        $uri    = new Uri("http://example.com/foo?foo[]=v1&foo[]=v2&bar=v3");
        parse_str($uri->getQuery(), $query);
        return [
            [
                $query,
                new ServerRequest($method, $uri, [], [], [], $query)
            ],
        ];
    }
    
    public function provideGetUploadedFiles(){
        return [
            
        ];
    }
    
    public function provideGetParsedBody(){
        return [
            
        ];
    }
    
    public function provideGetAttributes(){
        return [
            
        ];
    }
    
    public function provideGetAttribute(){
        return [
            
        ];
    }
}