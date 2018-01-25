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

use Fratily\Http\Message\Uri;

/**
 *
 */
class UriTest extends \PHPUnit\Framework\TestCase{
    
    /**
     * @dataProvider    provideURI
     */
    public function testParseURI(string $uri, $expected){
        $this->assertEquals($expected, Uri::parseUri($uri));
    }
    
    /**
     * @dataProvider    provideGetScheme
     */
    public function testGetScheme($uri, $expected){
        $uri    = new Uri($uri);
        
        $this->assertEquals($expected, $uri->getScheme());
    }
    
    /**
     * @dataProvider    provideGetAuthority
     */
    public function testGetAuthority($uri, $expected){
        $uri    = new Uri($uri);
        
        $this->assertEquals($expected, $uri->getAuthority());
    }
    
    /**
     * @dataProvider    provideGetUserInfo
     */
    public function testGetUserInfo($uri, $expected){
        $uri    = new Uri($uri);
        
        $this->assertEquals($expected, $uri->getUserInfo());
    }
    
    /**
     * @dataProvider    provideGetUser
     */
    public function testGetUser($uri, $expected){
        $uri    = new Uri($uri);
        
        $this->assertEquals($expected, $uri->getUser());
    }
    
    /**
     * @dataProvider    provideGetPassword
     */
    public function testGetPassword($uri, $expected){
        $uri    = new Uri($uri);
        
        $this->assertEquals($expected, $uri->getPassword());
    }
    
    /**
     * @dataProvider    provideGetHost
     */
    public function testGetHost($uri, $expected){
        $uri    = new Uri($uri);
        
        $this->assertEquals($expected, $uri->getHost());
    }
    
    /**
     * @dataProvider    provideGetPort
     */
    public function testGetPort($uri, $expected){
        $uri    = new Uri($uri);
        
        $this->assertEquals($expected, $uri->getPort());
    }
    
    /**
     * @dataProvider    provideGetPath
     */
    public function testGetPath($uri, $expected){
        $uri    = new Uri($uri);
        
        $this->assertEquals($expected, $uri->getPath());
    }
    
    /**
     * @dataProvider    provideGetQuery
     */
    public function testGetQuery($uri, $expected){
        $uri    = new Uri($uri);
        
        $this->assertEquals($expected, $uri->getQuery());
    }
    
    /**
     * @dataProvider    provideGetFragment
     */
    public function testGetFragment($uri, $expected){
        $uri    = new Uri($uri);
        
        $this->assertEquals($expected, $uri->getFragment());
    }
    
    
    public function provideGetScheme(){
        return [
            ["http://example.com", "http"],
            ["https://example.com", "https"],
            ["http://user:password@example.com", "http"],
            ["https://:password@example.com", "https"]
        ];
    }
    
    public function provideGetAuthority(){
        return [
            ["http://example.com", "example.com"],
            ["http://user@example.com", "user@example.com"],
            ["http://user:password@example.com", "user:password@example.com"],
            ["http://:password@example.com", ":password@example.com"]
        ];
    }
    
    public function provideGetUserInfo(){
        return [
            ["http://example.com", ""],
            ["http://user@example.com", "user"],
            ["http://user:password@example.com", "user:password"],
            ["http://:password@example.com", ":password"]
        ];
    }
    
    public function provideGetUser(){
        return [
            ["http://example.com", ""],
            ["http://user@example.com", "user"],
            ["http://user:password@example.com", "user"],
            ["http://:password@example.com", ""]
        ];
    }
    
    public function provideGetPassword(){
        return [
            ["http://example.com", ""],
            ["http://user@example.com", ""],
            ["http://user:password@example.com", "password"],
            ["http://:password@example.com", "password"]
        ];
    }
    
    public function provideGetHost(){
        return [
            ["http://example.com", "example.com"],
            ["http://[::ffff:192.168.1.32]", "[::ffff:192.168.1.32]"],
            ["http://192.168.1.32", "192.168.1.32"],
        ];
    }
    
    public function provideGetPort(){
        return [
            ["http://example.com:8080", 8080],
            ["http://example.com:80", null],
            ["https://example.com:443", null],
            ["http://example.com", null],
            ["https://example.com", null],
        ];
    }
    
    public function provideGetPath(){
        return [
            ["http://example.com/foo/bar", "/foo/bar"],
            ["http://example.com/", "/"],
            ["http://example.com", "/"],
        ];
    }
    
    public function provideGetQuery(){
        return [
            ["http://example.com?foo=value", "foo=value"],
            ["http://example.com?", ""],
            ["http://example.com", ""],
        ];
    }
    
    public function provideGetFragment(){
        return [
            ["http://example.com#foo", "foo"],
            ["http://example.com#", ""],
            ["http://example.com", ""],
        ];
    }
    
    public function provideGetCase(){
        return [
            [
                "http://example.com",
                [
                    "scheme"    => "http",
                    "authority" => "example.com",
                    "host"      => "example.com"
                ]
            ],
            [
                "https://example.com",
                [
                    "scheme"    => "https",
                    "authority" => "example.com",
                    "host"      => "example.com"
                ]
            ],
            [
                "http://192.168.1.32",
                [
                    "scheme"    => "http",
                    "authority" => "192.168.1.32",
                    "host"      => "192.168.1.32"
                ]
            ],
            [
                "https://[f::f:f:f]",
                [
                    "scheme"    => "https",
                    "authority" => "[f::f:f:f]",
                    "host"      => "[f::f:f:f]"
                ]
            ],
            [
                "https://[::ffff:192.168.1.32]",
                [
                    "scheme"    => "https",
                    "authority" => "[::ffff:192.168.1.32]",
                    "host"      => "[::ffff:192.168.1.32]"
                ]
            ],
            [
                "http://user@example.com",
                [
                    "scheme"    => "http",
                    "authority" => "user@example.com",
                    "userinfo"  => "user",
                    "user"      => "user",
                    "host"      => "example.com"
                ]
            ],
            [
                "http://user:passwd@example.com",
                [
                    "scheme"    => "http",
                    "authority" => "user:passwd@example.com",
                    "userinfo"  => "user:passwd",
                    "user"      => "user",
                    "password"  => "passwd",
                    "host"      => "example.com"
                ]
            ],
            [
                "http://example.com:8080",
                [
                    "scheme"    => "http",
                    "authority" => "example.com",
                    "host"      => "example.com",
                    "port"      => 8080
                ]
            ],
            [
                "http://example.com/",
                [
                    "scheme"    => "http",
                    "authority" => "example.com",
                    "host"      => "example.com",
                    "path"      => "/"
                ]
            ],
            [
                "http://example.com/foo/bar/",
                [
                    "scheme"    => "http",
                    "authority" => "example.com",
                    "host"      => "example.com",
                    "path"      => "/foo/bar/"
                ]
            ],
            [
                "http://example.com/foo/bar/baz.html",
                [
                    "scheme"    => "http",
                    "authority" => "example.com",
                    "host"      => "example.com",
                    "path"      => "/foo/bar/baz.html"
                ]
            ],
            [
                "http://example.com/foo/bar/baz.html?foo=foo_v",
                [
                    "scheme"    => "http",
                    "authority" => "example.com",
                    "host"      => "example.com",
                    "path"      => "/foo/bar/baz.html",
                    "query"     => "foo=foo_v"
                ]
            ],
            [
                "http://example.com/foo/bar/baz.html?foo=foo_v&bar=bar_v",
                [
                    "scheme"    => "http",
                    "authority" => "example.com",
                    "host"      => "example.com",
                    "path"      => "/foo/bar/baz.html",
                    "query"     => "foo=foo_v&bar=bar_v"
                ]
            ],
            [
                "http://example.com/foo/bar/baz.html?foo[]=foo_v1&foo[]=foo_v2",
                [
                    "scheme"    => "http",
                    "authority" => "example.com",
                    "host"      => "example.com",
                    "path"      => "/foo/bar/baz.html",
                    "query"     => "foo[]=foo_v1&foo[]=foo_v2"
                ]
            ]
        ];
    }
    
    public function testWith(){
        $uri    = new Uri("http://example.com:8080/foo/bar.html?baz=value");
        
        $this->assertEquals("http", $uri->getScheme());
        $uri    = $uri->withScheme("https");
        $this->assertEquals("https", $uri->getScheme());
        
        $this->assertEquals("", $uri->getUserInfo());
        $this->assertEquals("", $uri->getUser());
        $this->assertEquals("", $uri->getPassword());
        $uri    = $uri->withUserInfo("user", "passwd");
        $this->assertEquals("user:passwd", $uri->getUserInfo());
        $this->assertEquals("user", $uri->getUser());
        $this->assertEquals("passwd", $uri->getPassword());
        
        $this->assertEquals("example.com", $uri->getHost());
        $uri    = $uri->withHost("www.example.com");
        $this->assertEquals("www.example.com", $uri->getHost());
        
        $this->assertEquals(8080, $uri->getPort());
        $uri    = $uri->withPort(443);
        $this->assertEquals(null, $uri->getPort());
        
        $this->assertEquals("/foo/bar.html", $uri->getPath());
        $uri    = $uri->withPath("/foo/bar.xhtml");
        $this->assertEquals("/foo/bar.xhtml", $uri->getPath());
        
        $this->assertEquals("baz=value", $uri->getQuery());
        $uri    = $uri->withQuery("baz[]=v1&baz[]=v2");
        $this->assertEquals("baz[]=v1&baz[]=v2", $uri->getQuery());
        
        $this->assertEquals("", $uri->getFragment());
        $uri    = $uri->withFragment("hoge");
        $this->assertEquals("hoge", $uri->getFragment());
        
        $this->assertEquals("https://user:passwd@www.example.com/foo/bar.xhtml?baz[]=v1&baz[]=v2#hoge", (string)$uri);
    }
    
    public function provideURI(){
        return [
            [
                "http://example.com",
                [
                    "scheme"    => "http",
                    "host"      => "example.com"
                ]
            ],
            [
                "https://example.com",
                [
                    "scheme"    => "https",
                    "host"      => "example.com"
                ]
            ],
            [
                "http://192.168.1.32",
                [
                    "scheme"    => "http",
                    "host"      => "192.168.1.32"
                ]
            ],
            [
                "https://[f::f:f:f]",
                [
                    "scheme"    => "https",
                    "host"      => "[f::f:f:f]"
                ]
            ],
            [
                "https://[::ffff:192.168.1.32]",
                [
                    "scheme"    => "https",
                    "host"      => "[::ffff:192.168.1.32]"
                ]
            ],
            [
                "ftp://example.com",
                false
            ],
            [
                "http://user@example.com",
                [
                    "scheme"    => "http",
                    "userinfo"  => "user",
                    "host"      => "example.com"
                ]
            ],
            [
                "http://user:passwd@example.com",
                [
                    "scheme"    => "http",
                    "userinfo"  => "user:passwd",
                    "host"      => "example.com"
                ]
            ],
            [
                "http://use r:passwd@example.com",
                false
            ],
            [
                "http://example.com:8080",
                [
                    "scheme"    => "http",
                    "host"      => "example.com",
                    "port"      => 8080
                ]
            ],
            [
                "http://example.com:-123",
                false
            ],
            [
                "http://example.com/",
                [
                    "scheme"    => "http",
                    "host"      => "example.com",
                    "path"      => "/"
                ]
            ],
            [
                "http://example.com/foo/bar/",
                [
                    "scheme"    => "http",
                    "host"      => "example.com",
                    "path"      => "/foo/bar/"
                ]
            ],
            [
                "http://example.com/foo/bar/baz.html",
                [
                    "scheme"    => "http",
                    "host"      => "example.com",
                    "path"      => "/foo/bar/baz.html"
                ]
            ],
            [
                "http://example.com/foo/b ar/",
                false
            ],
            [
                "http://example.com/foo/bar/baz.html?foo=foo_v",
                [
                    "scheme"    => "http",
                    "host"      => "example.com",
                    "path"      => "/foo/bar/baz.html",
                    "query"     => "foo=foo_v"
                ]
            ],
            [
                "http://example.com/foo/bar/baz.html?foo=foo_v&bar=bar_v",
                [
                    "scheme"    => "http",
                    "host"      => "example.com",
                    "path"      => "/foo/bar/baz.html",
                    "query"     => "foo=foo_v&bar=bar_v"
                ]
            ],
            [
                "http://example.com/foo/bar/baz.html?foo[]=foo_v1&foo[]=foo_v2",
                [
                    "scheme"    => "http",
                    "host"      => "example.com",
                    "path"      => "/foo/bar/baz.html",
                    "query"     => "foo[]=foo_v1&foo[]=foo_v2"
                ]
            ],
            [
                "http://example.com/foo/bar/baz.html?foo=foo _v",
                false
            ],
            [
                "file:///C:/example",
                false
            ],
            [
                "file:///home/example",
                false
            ],
            [
                "http:///eee",
                false
            ]
        ];
    }
}