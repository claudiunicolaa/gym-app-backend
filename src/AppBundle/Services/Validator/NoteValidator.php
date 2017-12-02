<?php

namespace AppBundle\Services\Validator;

use AppBundle\Exception\NoteValidationException;

/**
 * Class NoteValidator
 *
 * @author Alexandru Emil Popa <a.pope95@yahoo.com>
 */
class NoteValidator
{
    const ALLOWED_KEYS = ['text'];
    const MANDATORY_CREATE_FIELDS = ['text'];

    /**
     * @param $queryParams
     *
     * @return void
     *
     * @throws NoteValidationException if mandatory fields are missing
     */
    public function checkMandatoryFields(array $queryParams) : void
    {
        if (1 !== count(array_intersect_key($queryParams, array_flip(self::MANDATORY_CREATE_FIELDS)))) {
            throw new NoteValidationException('Missing mandatory parameters!');
        }
    }

    /**
     * @param array $data
     *
     * @return void
     *
     * @throws NoteValidationException if the note text is empty or too many parameters given
     */
    public function validate(array $data) : void {
        $filteredData = array_intersect_key($data, array_flip(self::ALLOWED_KEYS));

        if (count($filteredData) !== count($data)) {
            throw new NoteValidationException("Invalid parameters given!");
        }

        if ($data['text'] === "") {
            throw new NoteValidationException('Note text can\'t be empty!');
        }

    }
}