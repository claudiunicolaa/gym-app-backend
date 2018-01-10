<?php

namespace AppBundle\Admin;

use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Class NoteAdmin
 *
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 */
class NoteAdmin extends AbstractBaseAdmin
{
    /**
     * @param string           $code
     * @param string           $class
     * @param string           $baseControllerName
     * @param EntityRepository $repository
     */
    public function __construct($code, $class, $baseControllerName, EntityRepository $repository)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->setRepository($repository);
    }


    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('text', 'text', ['required' => true])
            ->add('user', null, ['required' => true]);
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('text')
            ->add('creationDate')
            ->add('user');
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('text')
            ->addIdentifier('creationDate')
            ->addIdentifier('user');
    }

    /**
     * @inheritdoc
     */
    public function setImageTargetFolder(string $imageTargetFolder): void
    {
        $this->imageTargetFolder = '';
    }

    /**
     * @inheritdoc
     */
    public function prePersist($object)
    {
        return ;
    }

    /**
     * @inheritdoc
     */
    public function preUpdate($newObject)
    {
        return ;
    }

    /**
     * @inheritdoc
     */
    public function preRemove($object)
    {
        return ;
    }

    /**
     * @inheritdoc
     */
    public function preBatchAction($actionName, ProxyQueryInterface $query, array &$idx, $allElements)
    {
        return ;
    }
}
