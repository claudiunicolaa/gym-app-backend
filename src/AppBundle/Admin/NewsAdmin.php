<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Doctrine\ORM\EntityRepository;

/**
 * Class NewsAdmin
 *
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 */
class NewsAdmin extends AbstractBaseAdmin
{
    /**
     * @param string           $code
     * @param string           $class
     * @param string           $baseControllerName
     * @param EntityRepository $repository
     */
    public function __construct($code, $class, $baseControllerName, EntityRepository $repository) {
        parent::__construct($code, $class, $baseControllerName);

        $this->setImageTargetFolder('');
        $this->setRepository($repository);
    }

    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('text', 'text', ['required' => true])
            ->add('title', 'text', ['required' => true]);
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('text')
            ->add('title');
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('text')
            ->addIdentifier('title');
    }


    /**
     * @inheritdoc
     */
    public function setImageTargetFolder(string $imageTargetFolder): void
    {
        $this->imageTargetFolder = $imageTargetFolder;
    }

    /**
     * @inheritdoc
     */
    public function prePersist($object)
    {
    }

    /**
     * @inheritdoc
     */
    public function preUpdate($newObject)
    {
    }

    /**
     * @inheritdoc
     */
    public function preRemove($object)
    {
    }

    /**
     * @inheritdoc
     */
    public function preBatchAction($actionName, ProxyQueryInterface $query, array &$idx, $allElements)
    {
    }
}
