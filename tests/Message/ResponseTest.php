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

use Fratily\Http\Message\Response;

/**
 *
 */
class ResponseTest extends \PHPUnit\Framework\TestCase{
    
    /**
     * @dataProvider    provideGetStatusCode
     */
    public function testGetStatusCode($expected, $response){
        $this->assertEquals($expected, $response->getStatusCode());
    }
    
    /**
     * @dataProvider    provideGetReasonPhrase
     */
    public function testGetReasonPhrase($expected, $response){
        $this->assertEquals($expected, $response->getReasonPhrase());
    }
    
    public function testWith(){
        $response   = new Response(200);
        
        $response   = $response->withStatus(500);
        $this->assertEquals(500, $response->getStatusCode());
    }
    
    public function provideGetStatusCode(){
        return [
            [200, new Response(200)],
            [204, new Response(204)],
            [302, new Response(302)],
            [404, new Response(404)],
            [500, new Response(500)]
        ];
    }
    
    public function provideGetReasonPhrase(){
        return [
            ["OK", new Response(200)],
            ["No Content", new Response(204)],
            ["Found", new Response(302)],
            ["Not Found", new Response(404)],
            ["Internal Server Error", new Response(500)]
        ];
    }
}