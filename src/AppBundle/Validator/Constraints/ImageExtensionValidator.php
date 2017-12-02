<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class ImageExtensionValidator
 *
 * @author Ioan Ovidiu Enache <i.ovidiuenache@yahoo.com>
 */
class ImageExtensionValidator extends ConstraintValidator
{
    const SUPPORTED_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png'];

    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }

        if (!($value instanceof UploadedFile)) {
            $this->context->buildViolation("Invalid file given!")->addViolation();
        } else {
            $extension = strtolower($value->getClientOriginalExtension());
            if (!in_array($extension, self::SUPPORTED_IMAGE_EXTENSIONS)) {
                $this->context->buildViolation("Invalid file extension given!")->addViolation();
            }
        }
    }
}
