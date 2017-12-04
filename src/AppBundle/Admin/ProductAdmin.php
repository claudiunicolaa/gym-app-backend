<?php

namespace AppBundle\Admin;

use AppBundle\Services\Helper\FileHelper;
use AppBundle\Validator\Constraints\ImageExtension;
use Sonata\AdminBundle\Admin\AbstractAdmin;
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
class ProductAdmin extends AbstractAdmin
{
    /**
     * @var FileHelper
     */
    protected $fileHelper;

    /**
     * @inheritdoc
     */
    public function __construct($code, $class, $baseControllerName, FileHelper $fileHelper)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->fileHelper = $fileHelper;
    }

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
                'mapped' => false,
                'required' => false,
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
        $image = $this->getForm()->get('image')->getData();
        if (null !== $image) {
            $fileName = $this->fileHelper->uploadFile($image, 'product');
            $object->setImage($fileName);
        }
    }

    /**
     * @inheritdoc
     */
    public function preUpdate($newObject)
    {
        $image = $this->getForm()->get('image')->getData();
        if (null !== $image) {
            $this->fileHelper->removePicture($newObject);
            $fileName = $this->fileHelper->uploadFile($image, 'product');
            $newObject->setImage($fileName);
        }
    }
}
