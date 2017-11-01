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
    const ALLOWED_KEYS = ['eventDate', 'capacity', 'image', 'name'];

    /**
     * @param array $data
     *
     * @return void
     *
     * @throws CourseValidationException if the course data is invalid
     */
    public function validate(array $data) : void
    {
        $filteredData = array_intersect_key($data, array_flip(self::ALLOWED_KEYS));

        if (count($filteredData) !== count($data)) {
            throw new CourseValidationException("Invalid parameters given!");
        }

        foreach ($filteredData as $key => $value) {
            call_user_func([$this, 'validate'.ucwords($key)], $value);
        }
    }

    /**
     * @param string $eventDate
     *
     * @return void
     *
     * @throws \Exception
     */
    private function validateEventDate(string $eventDate) : void
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
     * @return void
     *
     * @throws \Exception
     */
    private function validateCapacity(string $capacity) : void
    {
        if (!is_numeric($capacity) || (int)$capacity < 0) {
            throw new CourseValidationException("Invalid capacity given!");
        }
    }

    /**
     * @param string $image
     *
     * @return void
     *
     * @throws \Exception
     */
    private function validateImage(string $image) : void
    {
        if (!is_string($image) || !filter_var($image, FILTER_VALIDATE_URL)) {
            throw new CourseValidationException("Invalid image url given!");
        }
    }

    /**
     * @param string $name
     *
     * @return void
     *
     * @throws \Exception
     */
    private function validateName(string $name) : void
    {
        if (!is_string($name) || strlen($name) === 0) {
            throw new CourseValidationException("Invalid name given!");
        }
    }
}
