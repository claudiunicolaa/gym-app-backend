<?php


namespace AppBundle\Admin;

use AppBundle\Services\Helper\FileHelper;
use Sonata\AdminBundle\Admin\AbstractAdmin;

/**
 * Class BaseAdmin
 * @author Claudiu Nicola <claudiunicola96@gmail.com>
 */
class BaseAdmin extends AbstractAdmin
{
    /**
     * @var FileHelper
     */
    protected $fileHelper;

    /**
     * @param string     $code
     * @param string     $class
     * @param string     $baseControllerName
     * @param FileHelper $fileHelper
     */
    public function __construct($code, $class, $baseControllerName, FileHelper $fileHelper)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->fileHelper = $fileHelper;
    }

    /**
     * @param        $object
     * @param string $targetFolder
     * @return void
     */
    public function manageImageUpload($object, string $targetFolder): void
    {
        $image = $this->getForm()->get('image')->getData();
        if (null !== $image) {
            $this->fileHelper->removePicture($object);
            $fileName = $this->fileHelper->uploadFile($image, $targetFolder);
            $object->setImage($fileName);
        }
    }
}
