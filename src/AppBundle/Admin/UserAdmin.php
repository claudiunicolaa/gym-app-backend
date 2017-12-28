<?php

namespace AppBundle\Admin;

use AppBundle\Validator\Constraints\ImageExtension;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use AppBundle\Services\Helper\FileHelper;
use Doctrine\ORM\EntityRepository;

/**
 * Class UserAdmin
 *
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 * @author Claudiu Nicola <claudiunicola96@gmail.com>
 */
class UserAdmin extends AbstractBaseAdmin
{
    /**
     * @param string           $code
     * @param string           $class
     * @param string           $baseControllerName
     * @param FileHelper       $fileHelper
     * @param EntityRepository $repository
     */
    public function __construct(
        $code,
        $class,
        $baseControllerName,
        FileHelper $fileHelper,
        EntityRepository $repository
    ) {
        parent::__construct($code, $class, $baseControllerName);

        $this->setFileHelper($fileHelper);
        $this->setRepository($repository);
        $this->setImageTargetFolder('user');
    }

    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('email', 'email', ['required' => true])
            ->add('firstName', 'text', ['required' => true])
            ->add('lastName', 'text', ['required' => true])
            ->add('plainPassword', 'password', [
                'required' => false,
                'label'    => 'Password',
            ])
            ->add('roles', 'choice', [
                'required' => false,
                'multiple' => true,
                'choices'  => [
                    'Regular User'  => "ROLE_USER",
                    'Trainer'       => 'ROLE_TRAINER',
                    'Administrator' => 'ROLE_ADMIN'
                ]
            ])
            ->add('image', 'file', [
                'mapped'      => false,
                'required'    => false,
                'constraints' => new ImageExtension()
            ])
            ->add('isAtTheGym');
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('email')
            ->add('firstName')
            ->add('lastName')
            ->add('isAtTheGym');
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('email')
            ->add('firstName')
            ->add('lastName')
            ->add('isAtTheGym', 'choice', [
                'editable' => true,
                'choices'  => [0 => 'no', 1 => 'yes'],

            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                ]
            ]);
    }

    /**
     * @inheritdoc
     *
     * Used because the FOS username cannot be null
     */
    public function prePersist($object)
    {
        parent::prePersist($object);
        $object->setUsername($object->getEmail());
    }

    /**
     * @inheritdoc
     */
    public function setImageTargetFolder(string $imageTargetFolder): void
    {
        $this->imageTargetFolder = $imageTargetFolder;
    }
}
