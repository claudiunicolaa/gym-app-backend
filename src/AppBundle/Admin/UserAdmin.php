<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class UserAdmin
 *
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 */
class UserAdmin extends AbstractAdmin
{
    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('email', 'email', ['required' => true])
            ->add('firstName', 'text', ['required' => true])
            ->add('lastName', 'text', ['required' => true])
            ->add(
                'plainPassword',
                'password',
                [
                    'required' => true,
                    'help'     => 'Currently any password is valid! A validation will be implemented soon!'
                ]
            )
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
            ->add('isAtTheGym',
                'choice',
                [
                    'editable' => true,
                    'choices'  => [0 => 'no', 1 => 'yes'],

                ]);
    }

    /**
     * @inheritdoc
     *
     * Used because the FOS username cannot be null
     */
    public function prePersist($object)
    {
        $object->setUsername($object->getEmail());
    }
}
