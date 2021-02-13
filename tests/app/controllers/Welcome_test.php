<?php

declare(strict_types=1);

class Welcome_test extends TestCase
{
    public function test_index(): void
    {
        $output = $this->request('GET', 'welcome');
        $this->assertContains('<title>CodeIgniterへようこそ！</title>', $output);
    }
}
