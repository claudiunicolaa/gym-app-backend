<?php

namespace AppBundle\Services\Validator;

use AppBundle\Exception\UserValidationException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class UserValidator
 *
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 */
class UserValidator
{
    const ALLOWED_KEYS = ['email', 'fullName', 'picture', 'password'];
    const MANDATORY_REGISTER_FIELDS = ['email', 'password', 'fullName'];
    const SUPPORTED_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png'];

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
        $email = trim($email);
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
        $fullName = trim($fullName);
        if ('' === $fullName || strpos($fullName, ' ') === false) {
            throw new UserValidationException("Invalid full name given!");
        }
    }

    /**
     * @param UploadedFile|string|null $picture
     *
     * @return void
     *
     * @throws UserValidationException if the picture is not valid
     */
    private function validatePicture($picture) : void
    {
        if (!($picture instanceof UploadedFile)) {
            throw new UserValidationException("Invalid picture given!");
        }

        $extension = strtolower($picture->getClientOriginalExtension());
        if (!in_array($extension, self::SUPPORTED_IMAGE_EXTENSIONS)) {
            throw new UserValidationException("Invalid picture extension given!");
        }

        if (strlen($picture->getClientOriginalName()) === 0) {
            throw new UserValidationException("Picture must have a name!");
        }
    }

    /**
     * Acceptance criteria:
     *  - minimum length of 8 characters
     *  - at least one lowercase letter
     *  - at least one uppercase letter
     *  - at lest one number
     *
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
