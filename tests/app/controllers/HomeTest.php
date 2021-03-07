<?php

declare(strict_types=1);

namespace App\Controllers;

use Kenjis\CI3Compatible\Test\TestCase\FeatureTestCase;

class HomeTest extends FeatureTestCase
{
    public function test_index(): void
    {
        $output = $this->request('GET', '/');
        $this->assertStringContainsString('<title>CodeIgniterへようこそ！</title>', $output);
    }
}
