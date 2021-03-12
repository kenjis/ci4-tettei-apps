<?php

declare(strict_types=1);

namespace App\Libraries\Validation;

use CodeIgniter\Validation\Validation;
use Kenjis\CI3Compatible\Exception\RuntimeException;

class FieldValidation
{
    /** @var Validation */
    private $validation;

    public function __construct(Validation $validation)
    {
        $this->validation =  $validation;
    }

    /**
     * @param mixed                 $value
     * @param mixed                 $rules
     * @param array<string, string> $errors
     */
    public function validate($value, $rules, array $errors = []): bool
    {
        if ($this->validation->check($value, $rules, $errors)) {
            return true;
        }

        throw new RuntimeException('不正な入力です。');
    }
}
