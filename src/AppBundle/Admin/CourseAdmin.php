<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Validator\Constraints\GreaterThan;

/**
 * Class CourseAdmin
 *
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 */
class CourseAdmin extends BaseAdmin
{
    /**
     * @inheritdoc
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', 'text', ['required' => true])
            ->add('trainer', null, ['required' => true])
            ->add(
                'timestamp',
                'number',
                [
                    'required' => true,
                    'help'     => 'A valid timestamp will be one greater than the present one.'
                ]
            )
            ->add('capacity', 'number', ['required' => true])
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
            ->add('name')
            ->add('trainer')
            ->add('eventDate')
            ->add('capacity');
    }

    /**
     * @inheritdoc
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('name')
            ->addIdentifier('trainer')
            ->addIdentifier('timestamp', 'number')
            ->addIdentifier('capacity')
            ->addIdentifier('image');
    }

    /**
     * @inheritdoc
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('timestamp')
            ->addConstraint(new GreaterThan((new \DateTime())->getTimestamp()))
            ->end()
            ->with('capacity')
            ->addConstraint(new GreaterThan(0))
            ->end();
    }

    /**
     * @inheritdoc
     */
    public function prePersist($object)
    {
        $this->manageImageUpload($object, 'course');
    }

    /**
     * @inheritdoc
     */
    public function preUpdate($newObject)
    {
        $this->manageImageUpload($newObject, 'course');
    }
}
