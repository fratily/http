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

use Fratily\Http\Message\Request;
use Fratily\Http\Message\Uri;

/**
 *
 */
class RequestTest extends \PHPUnit\Framework\TestCase{
    
    /**
     * @dataProvider    provideGetRequestTarget
     */
    public function testGetRequestTarget($expected, $request){
        $this->assertEquals($expected, $request->getRequestTarget());
    }
    
    /**
     * @dataProvider    provideGetMethod
     */
    public function testGetMethod($expected, $request){
        $this->assertEquals($expected, $request->getMethod());
    }
    
    /**
     * @dataProvider    provideGetUri
     */
    public function testGetUri($expected, $request){
        $this->assertEquals($expected, $request->getUri());
    }
    
    public function testWith(){
        $request    = new Request("HEAD", new Uri("http://example.com"));
        
        $request    = $request->withRequestTarget("/foo?hoge=v1");
        $this->assertEquals("/foo?hoge=v1", $request->getRequestTarget());
        $this->assertEquals("/foo", $request->getUri()->getPath());
        $this->assertEquals("hoge=v1", $request->getUri()->getQuery());
        
        $request    = $request->withMethod("GET");
        $this->assertEquals("GET", $request->getMethod());
        
        $uri        = $request->getUri()->withHost("www.example.com");
        $request    = $request->withUri($uri);
        $this->assertEquals($uri, $request->getUri());
    }
    
    public function provideGetRequestTarget(){
        $method = "GET";
        return [
            [
                "/",
                new Request($method, new Uri("http://example.com/"))
            ],
            [
                "/?hoge=v1",
                new Request($method, new Uri("http://example.com/?hoge=v1"))
            ],
            [
                "/foo",
                new Request($method, new Uri("http://example.com/foo"))
            ],
            [
                "/foo?hoge=v1",
                new Request($method, new Uri("http://example.com/foo?hoge=v1"))
            ],
            [
                "/foo/",
                new Request($method, new Uri("http://example.com/foo/"))
            ],
            [
                "/foo/?hoge=v1",
                new Request($method, new Uri("http://example.com/foo/?hoge=v1"))
            ],
        ];
    }
    
    public function provideGetMethod(){
        $uri    = new Uri("http://example.com");
        
        return [
            ["GET", new Request("GET", $uri)],
            ["POST", new Request("POST", $uri)],
            ["PATCH", new Request("PATCH", $uri)],
            ["HEAD", new Request("HEAD", $uri)]
        ];
    }
    
    public function provideGetUri(){
        $uri1   = new Uri("http://example.com");
        $uri2   = new Uri("http://example.com/foo/bar");
        
        return [
            [$uri1, new Request("GET", $uri1)],
            [$uri2, new Request("POST", $uri2)]
        ];
    }
}