<?php

declare(strict_types=1);

namespace App\Libraries\Validation;

use App\Exception\RuntimeException;
use CodeIgniter\Validation\ValidationInterface;

class FieldValidation
{
    /** @var ValidationInterface */
    private $validation;

    public function __construct(ValidationInterface $validation)
    {
        $this->validation = $validation;
    }

    /**
     * @param array<bool|float|int|object|string|null>|bool|float|int|object|string|null $value
     * @param array<string, string>                                                      $errors
     */
    public function validate($value, string $rules, array $errors = []): bool
    {
        if ($this->validation->check($value, $rules, $errors)) {
            return true;
        }

        throw new RuntimeException('不正な入力です。');
    }
}
