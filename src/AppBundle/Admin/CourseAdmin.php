<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Course;
use AppBundle\Repository\CourseRepository;
use AppBundle\Services\Helper\FileHelper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

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

    /**
     * @var CourseRepository
     */
    protected $courseRepository;

    /**
     * @inheritdoc
     */
    public function __construct($code, $class, $baseControllerName, FileHelper $fileHelper, CourseRepository $courseRepository)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->fileHelper = $fileHelper;
        $this->courseRepository = $courseRepository;
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
                    'help' => 'A valid timestamp will be one greater than the present one.'
                ]
            )
            ->add('capacity', 'number', ['required' => true]);
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
            ->addIdentifier('capacity');
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

    public function preRemove($object)
    {
        /** @var Course $object */
        $this->fileHelper->removePicture($object);
    }

    public function preBatchAction($actionName, ProxyQueryInterface $query, array &$idx, $allElements)
    {
        if ('delete' === $actionName) {
            if ($allElements) {
                $allCourses = $this->courseRepository->findAll();
                /** @var Course $product */
                foreach ($allCourses as $course) {
                    $this->fileHelper->removePicture($course);
                }
            } else {
                foreach ($idx as $id) {
                    /** @var Course $course */
                    $course = $this->courseRepository->find($id);
                    $this->fileHelper->removePicture($course);
                }
            }
        }
    }
}
