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
namespace Fratily\Http\Message\Exception;

class UploadFileException extends \RuntimeException{

    const ERROR_MAP = [
        UPLOAD_ERR_OK           => "",
        UPLOAD_ERR_INI_SIZE     => "",
        UPLOAD_ERR_FORM_SIZE    => "",
        UPLOAD_ERR_PARTIAL      => "",
        UPLOAD_ERR_NO_FILE      => "",
        UPLOAD_ERR_NO_TMP_DIR   => "",
        UPLOAD_ERR_CANT_WRITE   => "",
        UPLOAD_ERR_EXTENSION    => "",
    ];

    public static function uploadError(int $error){
        return new static(self::ERROR_MAP[$error] ?? "");
    }

    public static function moved(){
        return new static("");
    }
}