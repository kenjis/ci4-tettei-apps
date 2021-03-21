<?php

declare(strict_types=1);

namespace App\Libraries\Validation;

use App\Models\Form\FormForm;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\URI;
use CodeIgniter\HTTP\UserAgent;
use Config\App;
use Config\Services;
use Kenjis\CI3Compatible\Test\TestCase\UnitTestCase;

class FormValidationTest extends UnitTestCase
{
    /** @var FormValidation */
    private $validation;

    public function setUp(): void
    {
        parent::setUp();

        $this->validation = new FormValidation(Services::validation());
    }

    public function test_検証が失敗するとfalseが返る(): void
    {
        $_POST = [];
        $request = new IncomingRequest(new App(), new URI(), null, new UserAgent());

        $validated = $this->validation->validate(
            $request,
            new FormForm()
        );

        $this->assertFalse($validated);
    }

    public function test_検証が成功するとtrueが返る(): void
    {
        $_POST = [
            'name' => '名前です',
            'email' => 'foo@example.com',
            'comment' => 'コメントです',
        ];
        $request = new IncomingRequest(new App(), new URI(), null, new UserAgent());

        $validated = $this->validation->validate(
            $request,
            new FormForm()
        );

        $this->assertTrue($validated);
    }
}
