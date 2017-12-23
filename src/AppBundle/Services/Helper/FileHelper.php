<?php

namespace AppBundle\Services\Helper;

use AppBundle\Entity\Course;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;

/**
 * Class FileHelper
 *
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 */
class FileHelper
{
    const IMAGE_NAME_SIZE = 8;
    const ALLOWED_TARGET_FOLDERS = ['user', 'course', 'product'];
    const CHARACTERS = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const UPLOADS_FOLDER_NAME = 'uploads';
    const USER_UPLOADS_FOLDER_NAME = self::UPLOADS_FOLDER_NAME . '/user';
    const COURSE_UPLOADS_FOLDER_NAME = self::UPLOADS_FOLDER_NAME . '/course';
    const PRODUCT_UPLOADS_FOLDER_NAME = self::UPLOADS_FOLDER_NAME . '/product';

    /**
     * @var string
     */
    private $webRoot;

    /**
     * FileHelper constructor.
     *
     * @param string $rootDir
     */
    public function __construct(string $rootDir)
    {
        $this->webRoot = realpath($rootDir . '/../web');
    }

    /**
     * @param int $length
     *
     * @return string
     */
    public function generateRandomString (int $length) : string
    {
        $charactersLength = strlen(self::CHARACTERS);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= self::CHARACTERS[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * @param UploadedFile|null  $file
     * @param string             $targetFolder
     *
     * @return string|null
     */
    public function uploadFile(?UploadedFile $file, string $targetFolder) : ?string
    {
        if (!in_array(strtolower($targetFolder), self::ALLOWED_TARGET_FOLDERS) ||
            null === $file
        ) {
            return null;
        }

        $fileExtension = '.' . $file->getClientOriginalExtension();
        $imagesLocation = $this->webRoot . '/' . self::UPLOADS_FOLDER_NAME .'/' . $targetFolder . '/';
        $existingImages = glob($this->webRoot . '/uploads/' . $targetFolder . '/*.*');
        do {
            $fileName = $this->generateRandomString(self::IMAGE_NAME_SIZE) . $fileExtension;
            $fullImagePath = $imagesLocation . $fileName;
        } while (in_array($fullImagePath, $existingImages));

        $file->move($imagesLocation, $fileName);

        return $fileName;
    }

    /**
     * @param User|Course|Product $entity
     *
     * @throws InvalidArgumentException if the argument is invalid
     */
    public function removeImage($entity) : void
    {
        if (!($entity instanceof User) && !($entity instanceof Course) && !($entity instanceof Product)) {
            throw new InvalidArgumentException("Invalid object given!");
        }

        $fileSystem = new Filesystem();
        $file = null;
        if ($entity instanceof User) {
            if ($entity->getImage() === User::DEFAULT_IMAGE_NAME) {
                return ;
            }

            try {
                $file = new File($this->webRoot . '/' . self::USER_UPLOADS_FOLDER_NAME . '/' . $entity->getImage());
                $fileSystem->remove($file);
            } catch (FileNotFoundException|IOException $ignored) {}

            return ;
        }

        if ($entity instanceof Product) {
            if ($entity->getImage() === Product::DEFAULT_IMAGE_NAME) {
                return ;
            }

            try {
                $file = new File($this->webRoot . '/' . self::PRODUCT_UPLOADS_FOLDER_NAME . '/' . $entity->getImage());
                $fileSystem->remove($file);
            } catch (FileNotFoundException|IOException $ignored) {}

            return ;
        }

        try {
            if ($entity->getImage() === Course::DEFAULT_IMAGE_NAME) {
                return ;
            }

            $file = new File($this->webRoot . '/' . self::COURSE_UPLOADS_FOLDER_NAME . '/' . $entity->getImage());
            $fileSystem->remove($file);
        }  catch (FileNotFoundException|IOException $ignored) {}
    }
}
