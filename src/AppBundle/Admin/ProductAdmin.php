<?php

namespace AppBundle\Admin;

use AppBundle\Validator\Constraints\ImageExtension;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Validator\Constraints\GreaterThan;

/**
 * Class ProductAdmin
 *
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 */
class ProductAdmin extends BaseAdmin
{
    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('price', 'number', ['required' => true])
            ->add('name', 'text', ['required' => true])
            ->add('category', 'text', ['required' => true])
            ->add('description', 'text', ['required' => false])
            ->add('image', 'file', [
                'mapped'      => false,
                'required'    => false,
                'constraints' => new ImageExtension()
            ]);
    }

    /**
     * @inheritdoc
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('price')
            ->add('name')
            ->add('category');
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('price')
            ->addIdentifier('image')
            ->addIdentifier('name')
            ->addIdentifier('category')
            ->addIdentifier('description');
    }

    /**
     * @inheritdoc
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('price')
            ->addConstraint(new GreaterThan(0))
            ->end();
    }

    /**
     * @inheritdoc
     */
    public function prePersist($object)
    {
        $this->manageImageUpload($object, 'product');
    }

    /**
     * @inheritdoc
     */
    public function preUpdate($newObject)
    {
        $this->manageImageUpload($newObject, 'product');
    }
}
