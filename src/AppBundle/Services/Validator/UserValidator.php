<?php

namespace AppBundle\Services\Validator;

use AppBundle\Exception\UserValidationException;

/**
 * Class UserValidator
 *
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 */
class UserValidator
{
    const ALLOWED_KEYS = ['email', 'fullName', 'picture', 'password'];
    const MANDATORY_REGISTER_FIELDS = ['email', 'password', 'fullName'];

    /**
     * @param $queryParams
     *
     * @return void
     *
     * @throws UserValidationException if mandatory fields are missing
     */
    public function checkMandatoryFields(array $queryParams) : void
    {
        if (3 !== count(array_intersect_key($queryParams, array_flip(self::MANDATORY_REGISTER_FIELDS)))) {
            throw new UserValidationException('Missing mandatory parameters!');
        }
    }

    /**
     * @param array $data
     *
     * @return void
     *
     * @throws UserValidationException if the course data is invalid
     */
    public function validate(array $data) : void
    {
        $filteredData = array_intersect_key($data, array_flip(self::ALLOWED_KEYS));

        if (count($filteredData) !== count($data)) {
            throw new UserValidationException("Invalid parameters given!");
        }

        foreach ($filteredData as $key => $value) {
            call_user_func([$this, 'validate'.ucwords($key)], $value);
        }
    }

    /**
     * @param string $email
     *
     * @return void
     *
     * @throws UserValidationException if the email is not valid
     */
    private function validateEmail(string $email) : void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new UserValidationException("Invalid email given!");
        }
    }

    /**
     * @param string $fullName
     *
     * @return void
     *
     * @throws UserValidationException if the full name is not valid
     */
    private function validateFullName(string $fullName) : void
    {
        if ('' === $fullName) {
            throw new UserValidationException("Invalid full name given!");
        }
    }

    /**
     * @param string $picture
     *
     * @return void
     *
     * @throws UserValidationException if the picture is not valid
     */
    private function validatePicture(string $picture) : void
    {
        if (!filter_var($picture, FILTER_VALIDATE_URL)) {
            throw new UserValidationException("Invalid image url given!");
        }
    }

    /**
     * @param string $password
     *
     * @return void
     *
     * @throws UserValidationException is the password is not valid
     */
    private function validatePassword(string $password) : void
    {
        if (0 === preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $password)) {
            throw new UserValidationException("Invalid password given!");
        }
    }
}
