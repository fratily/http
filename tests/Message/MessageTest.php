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

use Fratily\Http\Message\Message;
use Fratily\Http\Message\Stream\MemoryStream;

/**
 *
 */
class MessageTest extends \PHPUnit\Framework\TestCase{
    
    /**
     * @dataProvider    provideGetProtocolVersion
     */
    public function testGetProtocolVersion($expected, $message){
        $this->assertEquals($expected, $message->getProtocolVersion());
    }
    
    /**
     * @dataProvider    provideGetHeaders
     */
    public function testGetHeaders($expected, $message){
        $this->assertEquals($expected, $message->getHeaders());
    }
    
    /**
     * @dataProvider    provideGetHeader
     */
    public function testGetHeader($name, $expected, $message){
        $this->assertEquals($expected, $message->getHeader($name));
    }
    
    /**
     * @dataProvider    provideGetHeaderLine
     */
    public function testGetHeaderLine($name, $expected, $message){
        $this->assertEquals($expected, $message->getHeaderLine($name));
    }
    
    /**
     * @dataProvider    provideGetBody
     */
    public function testGetBody($expected, $message){
        $this->assertEquals($expected, $message->getBody());
    }
    
    public function testWith(){
        $msg    = new Message([], new MemoryStream(), "1.0");
        
        $msg    = $msg->withProtocolVersion("1.1");
        $this->assertEquals("1.1", $msg->getProtocolVersion());
        
        $this->assertEquals(false, $msg->hasHeader("host"));
        $msg    = $msg->withHeader("Host", "example.com");
        $this->assertEquals(["example.com"], $msg->getHeader("host"));
        $this->assertEquals(true, $msg->hasHeader("host"));
        
        $msg    = $msg->withAddedHeader("X-My-Data", "Foo");
        $msg    = $msg->withAddedHeader("X-my-data", "Bar");
        $msg    = $msg->withAddedHeader("x-My-data", "Baz");
        $this->assertEquals(["Foo", "Bar", "Baz"], $msg->getHeader("x-my-data"));
        
        $this->assertEquals(false, $msg->hasHeader("X-My-Remove"));
        $msg    = $msg->withHeader("X-My-Remove", "remove data");
        $this->assertEquals(["remove data"], $msg->getHeader("x-my-Remove"));
        $this->assertEquals(true, $msg->hasHeader("x-my-Remove"));
        $msg    = $msg->withoutHeader("x-my-Remove");
        $this->assertEquals(false, $msg->hasHeader("x-my-Remove"));
        
        $body   = new MemoryStream();
        $msg    = $msg->withBody($body);
        $this->assertEquals($body, $msg->getBody());
    }
    
    public function provideGetProtocolVersion(){
        $headers    = [];
        $body       = new MemoryStream();
        
        return [
            ["1.0", new Message($headers, $body, "1.0")],
            ["1.1", new Message($headers, $body, "1.1")],
            ["2", new Message($headers, $body, "2")],
        ];
    }
    
    public function provideGetHeaders(){
        $message    = new Message(
            [
                "Host"  => ["example.com"],
                "X-My-Data"     => ["Foo", "Bar", "Baz"]
            ],
            new MemoryStream(),
            "1.1"
        );
        
        return [
            [
                [
                    "Host"  => ["example.com"],
                    "X-My-Data"     => ["Foo", "Bar", "Baz"]
                ],
                $message
            ]
        ];
    }
    
    public function provideGetHeader(){
        $message    = new Message(
            [
                "Host"  => "example.com",
                "X-My-Data"     => ["Foo", "Bar", "Baz"]
            ],
            new MemoryStream(),
            "1.1"
        );
        
        return [
            [
                "host",
                ["example.com"],
                $message
            ],
            [
                "host",
                ["example.com"],
                $message
            ],
            [
                "HOST",
                ["example.com"],
                $message
            ],
            [
                "x-my-data",
                ["Foo", "Bar", "Baz"],
                $message
            ],
        ];
    }
    
    public function provideGetHeaderLine(){
        $message    = new Message(
            [
                "Host"  => "example.com",
                "X-My-Data"     => ["Foo", "Bar", "Baz"]
            ],
            new MemoryStream(),
            "1.1"
        );
        
        return [
            [
                "host",
                "example.com",
                $message
            ],
            [
                "host",
                "example.com",
                $message
            ],
            [
                "HOST",
                "example.com",
                $message
            ],
            [
                "x-my-data",
                "Foo,Bar,Baz",
                $message
            ],
        ];
    }
    
    public function provideGetBody(){
        $body       = new MemoryStream;
        $message    = new Message(
            [],
            $body
        );
        
        return [
            [
                $body,
                $message
            ]
        ];
    }
}