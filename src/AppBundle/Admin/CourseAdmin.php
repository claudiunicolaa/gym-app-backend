<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Course;
use AppBundle\Services\Helper\FileHelper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
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
class CourseAdmin extends AbstractAdmin
{
    /**
     * @var FileHelper
     */
    protected $fileHelper;

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
                'required'   => false,
                'data_class' => null
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
    public function preUpdate($object)
    {
        $this->manageFileUpload($object);
    }


    public function manageFileUpload(Course $object)
    {
        $request = $this->getRequest();
        $uniqId  = $this->getUniqid();
//        $uniqId     = $request->query->get('uniqid');
        $image      = $request->files->get($uniqId)['image'];
        $fileHelper = $this->get(FileHelper::class);
        $fileHelper->removePicture($object);
        $fileHelper->uploadFile($image, 'course');
    }
}
