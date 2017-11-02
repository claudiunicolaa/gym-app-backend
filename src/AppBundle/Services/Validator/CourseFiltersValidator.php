<?php

namespace AppBundle\Services\Validator;

use AppBundle\Exception\CourseRepositoryException;

/**
 * Class CourseFiltersValidator
 *
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 */
class CourseFiltersValidator
{
    const ALLOWED_FILTERS = ['users_courses', 'owned_courses', 'interval_start', 'interval_stop'];

    /**
     * @param array $data
     *
     * @return void
     *
     * @throws CourseRepositoryException if the course data is invalid
     */
    public function validate(array $data) : void
    {
        $filteredData = array_intersect_key($data, array_flip(self::ALLOWED_FILTERS));

        if (count($filteredData) !== count($data)) {
            throw new CourseRepositoryException("Invalid query params given!");
        }

        foreach ($filteredData as $key => $value) {
            call_user_func([$this, 'validate'.ucwords($key)], $value);
        }
    }

    /**
     * @param string $data
     *
     * @return void
     *
     * @throws CourseRepositoryException if data is not valid
     */
    private function validateUsers_courses(string $data) : void
    {
        if (!in_array(strtolower($data), ['true', 'false'])) {
            throw new CourseRepositoryException('Invalid value for users_courses parameter!');
        }
    }

    /**
     * @param string $data
     *
     * @return void
     *
     * @throws CourseRepositoryException if data is not valid
     */
    private function validateOwned_courses(string $data) : void
    {
        if (!in_array(strtolower($data), ['true', 'false'])) {
            throw new CourseRepositoryException('Invalid value for owned_courses parameter!');
        }
    }

    /**
     * @param string $data
     *
     * @return void
     *
     * @throws CourseRepositoryException if data is not valid
     */
    private function validateInterval_start(string $data) : void
    {
        if (!is_numeric($data) || (int)$data > 2554416000 || (int)$data < 0) {
            throw new CourseRepositoryException('Invalid value for interval_start parameter!');
        }
    }

    /**
     * @param string $data
     *
     * @return void
     *
     * @throws CourseRepositoryException if data is not valid
     */
    private function validateInterval_stop(string $data) : void
    {
        if (!is_numeric($data) || (int)$data < 0 || (int)$data > 2554416000) {
            throw new CourseRepositoryException('Invalid value for interval_stop parameter!');
        }
    }
}
