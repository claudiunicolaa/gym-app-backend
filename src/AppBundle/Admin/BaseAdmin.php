<?php


namespace AppBundle\Admin;

use AppBundle\Services\Helper\FileHelper;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

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
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @param string           $code
     * @param string           $class
     * @param string           $baseControllerName
     * @param FileHelper       $fileHelper
     * @param EntityRepository $repository
     */
    public function __construct($code, $class, $baseControllerName, FileHelper $fileHelper, EntityRepository $repository)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->fileHelper = $fileHelper;
        $this->repository = $repository;
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

    /**
     * @param object $object
     */
    public function preRemove($object)
    {
        $this->fileHelper->removePicture($object);
    }

    /**
     * @inheritdoc
     */
    public function preBatchAction($actionName, ProxyQueryInterface $query, array &$idx, $allElements)
    {
        if ('delete' === $actionName) {
            if ($allElements) {
                $entities = $this->repository->findAll();
                foreach ($entities as $entity) {
                    $this->fileHelper->removePicture($entity);
                }
            } else {
                foreach ($idx as $id) {
                    $entity = $this->repository->find($id);
                    $this->fileHelper->removePicture($entity);
                }
            }
        }
    }
}
