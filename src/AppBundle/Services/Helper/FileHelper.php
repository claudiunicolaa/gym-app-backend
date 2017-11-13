<?php

namespace AppBundle\Services\Helper;

use Symfony\Component\HttpFoundation\File\File;

/**
 * Class FileHelper
 *
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 */
class FileHelper
{
    /**
     * @param int $length
     *
     * @return string
     */
    public static function generateRandomString (int $length) : string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * @param File $file
     */
    public static function uploadFile(File $file) : void
    {
        $pictures = glob('web/uploads/user/*.*');
    }
}
