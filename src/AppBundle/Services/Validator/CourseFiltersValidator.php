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
    const ALLOWED_FILTERS = ['usersCourses', 'ownedCourses', 'intervalStart', 'intervalStop', 'expired'];

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
    private function validateUsersCourses(string $data) : void
    {
        if (!in_array(strtolower($data), ['true', 'false'])) {
            throw new CourseRepositoryException('Invalid value for usersCourses parameter!');
        }
    }

    /**
     * @param string $data
     *
     * @return void
     *
     * @throws CourseRepositoryException if data is not valid
     */
    private function validateOwnedCourses(string $data) : void
    {
        if (!in_array(strtolower($data), ['true', 'false'])) {
            throw new CourseRepositoryException('Invalid value for ownedCourses parameter!');
        }
    }

    /**
     * @param string $data
     *
     * @return void
     *
     * @throws CourseRepositoryException if data is not valid
     */
    private function validateIntervalStart(string $data) : void
    {
        if (!is_numeric($data) || (int)$data > 2554416000 || (int)$data < 0) {
            throw new CourseRepositoryException('Invalid value for intervalStart parameter!');
        }
    }

    /**
     * @param string $data
     *
     * @return void
     *
     * @throws CourseRepositoryException if data is not valid
     */
    private function validateIntervalStop(string $data) : void
    {
        if (!is_numeric($data) || (int)$data < 0 || (int)$data > 2554416000) {
            throw new CourseRepositoryException('Invalid value for intervalStop parameter!');
        }
    }

    /**
     * @param string $data
     *
     * @return void
     *
     * @throws CourseRepositoryException if data is not valid
     */
    private function validateExpired(string $data) : void
    {
        if (!in_array(strtolower($data), ['true', 'false'])) {
            throw new CourseRepositoryException('Invalid value for expired parameter!');
        }
    }
}
