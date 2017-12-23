<?php

namespace AppBundle\Admin;

use AppBundle\Services\Helper\FileHelper;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;

/**
 * Class AbstractBaseAdmin
 *
 * @author Claudiu Nicola <claudiunicola96@gmail.com>
 */
abstract class AbstractBaseAdmin extends AbstractAdmin
{
    /**
     * @var string
     */
    protected $imageTargetFolder;

    /**
     * @var FileHelper
     */
    protected $fileHelper;

    /**
     * @var EntityRepository
     */
    protected $repository;


    /**
     * Set the folder where the images we'll be saved.
     *
     * @param string $imageTargetFolder
     *
     * @return void
     */
    abstract public function setImageTargetFolder(string $imageTargetFolder): void;

    /**
     * @param        $object
     * @param string $targetFolder
     *
     * @return void
     *
     * @throws InvalidArgumentException if the argument is invalid
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
     * @inheritdoc
     */
    public function prePersist($object)
    {
        $this->manageImageUpload($object, $this->imageTargetFolder);
    }

    /**
     * @inheritdoc
     */
    public function preUpdate($newObject)
    {
        $this->manageImageUpload($newObject, $this->imageTargetFolder);
    }

    /**
     * @inheritdoc
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

    /**
     * @param FileHelper $fileHelper
     */
    public function setFileHelper(FileHelper $fileHelper)
    {
        $this->fileHelper = $fileHelper;
    }

    /**
     * @param EntityRepository $repository
     */
    public function setRepository(EntityRepository $repository)
    {
        $this->repository = $repository;
    }


}
