<?php

declare(strict_types=1);

namespace App\Controllers;

use Kenjis\CI3Compatible\Test\TestCase\FeatureTestCase;

class Welcome_test extends FeatureTestCase
{
    public function test_index(): void
    {
        $output = $this->request('GET', 'welcome');
        $this->assertStringContainsString('<title>CodeIgniterへようこそ！</title>', $output);
    }
}
