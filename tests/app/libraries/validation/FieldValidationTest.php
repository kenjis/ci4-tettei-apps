<?php

declare(strict_types=1);

namespace App\Libraries\Validation;

use Kenjis\CI3Compatible\Exception\RuntimeException;
use Kenjis\CI3Compatible\Test\TestCase\UnitTestCase;

class FieldValidationTest extends UnitTestCase
{
    public function test_検証がパスするとtrueが返る(): void
    {
        $validation = new FieldValidation();

        $catId = 1;
        $isValid = $validation->validate(
            $catId,
            'required|is_natural|max_length[11]'
        );

        $this->assertTrue($isValid);
    }

    public function test_検証に失敗すると例外が返る(): void
    {
        $validation = new FieldValidation();

        $this->expectException(RuntimeException::class);

        $catId = 'abc';
        $validation->validate(
            $catId,
            'required|is_natural|max_length[11]'
        );
    }

    public function test_複数回検証できる(): void
    {
        $validation = new FieldValidation();

        $catId = 1;
        $isValid = $validation->validate(
            $catId,
            'required|is_natural|max_length[1]'
        );
        $this->assertTrue($isValid);

        $q = 'CodeIgniter';
        $isValid = $validation->validate(
            $q,
            'max_length[11]'
        );

        $this->assertTrue($isValid);
    }
}
