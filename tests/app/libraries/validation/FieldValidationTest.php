<?php

declare(strict_types=1);

namespace App\Libraries\Validation;

use App\Exception\RuntimeException;
use Config\Services;
use Kenjis\CI3Compatible\Test\TestCase\UnitTestCase;

class FieldValidationTest extends UnitTestCase
{
    /** @var FieldValidation */
    private $validation;

    public function setUp(): void
    {
        parent::setUp();

        $this->validation = new FieldValidation(Services::validation());
    }

    public function test_検証がパスするとtrueが返る(): void
    {
        $catId = 1;
        $isValid = $this->validation->validate(
            $catId,
            'required|is_natural|max_length[11]'
        );

        $this->assertTrue($isValid);
    }

    public function test_検証に失敗すると例外が返る(): void
    {
        $this->expectException(RuntimeException::class);

        $catId = 'abc';
        $this->validation->validate(
            $catId,
            'required|is_natural|max_length[11]'
        );
    }

    public function test_複数回検証できる(): void
    {
        $catId = 1;
        $isValid = $this->validation->validate(
            $catId,
            'required|is_natural|max_length[1]'
        );
        $this->assertTrue($isValid);

        $q = 'CodeIgniter';
        $isValid = $this->validation->validate(
            $q,
            'max_length[11]'
        );

        $this->assertTrue($isValid);
    }
}
