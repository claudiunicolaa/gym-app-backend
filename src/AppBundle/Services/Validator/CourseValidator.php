<?php

namespace AppBundle\Services\Validator;

use AppBundle\Exception\CourseValidationException;

/**
 * Class CourseValidator
 *
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 */
class CourseValidator
{
    public function validate(string $key, ?string $value)
    {
        if ($value === null) {
            return ;
        }

        call_user_func([$this,'validate'.ucfirst($key)], $value);
//        switch ($key) {
//            case 'eventDate':
//                $this->validateEventDate($value);
//                break;
//            case 'capacity':
//                $this->validateCapacity($value);
//                break;
//            case 'image':
//                $this->validateImage($value);
//                break;
//            case 'name':
//                $this->validateName($value);
//                break;
//            default:
//                break;
//        }
    }

    /**
     * @param string $eventDate
     *
     * @throws \Exception
     */
    private function validateEventDate(string $eventDate)
    {
        if (!is_numeric($eventDate)) {
            throw new CourseValidationException("Invalid event date given!");
        }

        $date = new \DateTime();
        $date->setTimestamp($eventDate);
        if ($date < (new \DateTime())) {
            throw new CourseValidationException("Invalid event date given!");
        }
    }

    /**
     * @param string $capacity
     *
     * @throws \Exception
     */
    private function validateCapacity(string $capacity)
    {
        if (!is_numeric($capacity) || (int)$capacity < 0) {
            throw new CourseValidationException("Invalid capacity given!");
        }
    }

    /**
     * @param string $image
     *
     * @throws \Exception
     */
    private function validateImage(string $image)
    {
        if (!is_string($image) || !filter_var($image, FILTER_VALIDATE_URL)) {
            throw new CourseValidationException("Invalid image url given!");
        }
    }

    /**
     * @param string $name
     *
     * @throws \Exception
     */
    private function validateName(string $name)
    {
        if (!is_string($name) || strlen($name) === 0) {
            throw new CourseValidationException("Invalid name given!");
        }
    }
}
