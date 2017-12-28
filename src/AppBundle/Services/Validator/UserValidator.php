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
    const ALLOWED_KEYS_CREATE = ['email', 'fullName', 'image', 'password'];
    const ALLOWED_KEYS_UPDATE = ['fullName', 'image', 'password', 'isAtTheGym'];
    const MANDATORY_REGISTER_FIELDS = ['email', 'password', 'fullName'];
    const SUPPORTED_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png'];
    const SUPPORTED_BOOLEAN_VALUES = ['true', 'false'];

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
     * @param array     $data
     * @param string    $type
     *
     * @return void
     *
     * @throws UserValidationException if the course data is invalid
     */
    public function validate(array $data, string $type = 'create') : void
    {
        $allowedKeys = $type === 'create' ? self::ALLOWED_KEYS_CREATE : self::ALLOWED_KEYS_UPDATE;
        $filteredData = array_intersect_key($data, array_flip($allowedKeys));

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
        $email = trim(strtolower($email));
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
     * @param UploadedFile|string|null $image
     *
     * @return void
     *
     * @throws UserValidationException if the image is not valid
     */
    private function validateImage($image) : void
    {
        if (!($image instanceof UploadedFile)) {
            throw new UserValidationException("Invalid image given!");
        }

        $extension = strtolower($image->getClientOriginalExtension());
        if (!in_array($extension, self::SUPPORTED_IMAGE_EXTENSIONS)) {
            throw new UserValidationException("Invalid image extension given!");
        }

        if (strlen($image->getClientOriginalName()) === 0) {
            throw new UserValidationException("Image must have a name!");
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

    /**
     * @param string $isAtTheGym
     *
     * @return void
     *
     * @throws UserValidationException if the value is not valid
     */
    private function validateIsAtTheGym(string $isAtTheGym) : void
    {
        $isAtTheGym = trim(strtolower($isAtTheGym));
        if (!in_array($isAtTheGym, self::SUPPORTED_BOOLEAN_VALUES)) {
            throw new UserValidationException("Invalid value given for isAtTheGym!");
        }
    }
}
