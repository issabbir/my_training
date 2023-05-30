<?php
/**
 * Created by PhpStorm.
 * User: ashraf
 * Date: 3/9/20
 * Time: 11:59 AM
 */

namespace App\Enums;


class FileTypes
{
    private static $fileTypes = [
        'application/pdf' => 'application/pdf',
        'application/doc' => 'application/msword',
        'application/docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/png' => 'image/png',
        'image/jpg' => 'image/jpg',
        'image/jpeg' => 'image/jpeg',
    ];

    public static function getContentType($fileType)
    {
        $contentType = static::$fileTypes[$fileType];

        if($contentType) {
            return $contentType;
        }

        return '';
    }
}