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
namespace Fratily\Http\Status;

/**
 * 
 */
class MethodNotAllowed extends HttpStatus{
    
    const STATUS_CODE   = 405;
    
    /**
     * @var string[]
     */
    private $allowed;
    
    /**
     * Constructor
     * 
     * @param   string[]    $allowed
     * @param   string  $msg
     * @param   int $code
     */
    public function __construct(array $allowed, string $msg = "", int $code = 0){
        $this->allowed  = $allowed;
        
        parent::__construct($msg, $code);
    }
    
    /**
     * 
     * @return  string[]
     */
    public function getAllowed(){
        return $this->allowed;
    }
}