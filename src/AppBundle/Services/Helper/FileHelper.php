<?php

namespace AppBundle\Services\Helper;

use AppBundle\Admin\CourseAdmin;
use AppBundle\Entity\Course;
use AppBundle\Entity\User;
use Negotiation\Exception\InvalidArgument;
use Symfony\Component\Filesystem\Filesystem;
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
    const PICTURE_NAME_SIZE = 8;
    const ALLOWED_TARGET_FOLDERS = ['user', 'course'];
    const CHARACTERS = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

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
        $picturesLocation = $this->webRoot . '/uploads/' . $targetFolder . '/';
        $existingPictures = glob($this->webRoot . '/uploads/' . $targetFolder . '/*.*');
        do {
            $fileName = $this->generateRandomString(self::PICTURE_NAME_SIZE) . $fileExtension;
            $fullPicturePath = $picturesLocation . $fileName;
        } while (in_array($fullPicturePath, $existingPictures));

        $file->move($picturesLocation, $fileName);

        return $fileName;
    }

    /**
     * @param User|Course $entity
     *
     * @throws InvalidArgumentException if the argument is invalid
     */
    public function removePicture($entity) : void
    {
        if (!($entity instanceof User) || !($entity instanceof Course)) {
            throw new InvalidArgumentException("Invalid object given!");
        }

        $fileSystem = new Filesystem();
        if ($entity instanceof User) {
            $file = new File($this->webRoot . '/user/' . $entity->getPicturePath());
            if ($fileSystem->exists($file)) {
                $fileSystem->remove($file);
            }

            return ;
        }

        $file = new File($this->webRoot . '/course/' . $entity->getImagePath());
        if ($fileSystem->exists($file)) {
            $fileSystem->remove($file);
        }
    }
}
