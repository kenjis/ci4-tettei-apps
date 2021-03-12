<?php

declare(strict_types=1);

namespace App\Models\Shop;

use CodeIgniter\Test\CIUnitTestCase;

class CategoryTest extends CIUnitTestCase
{
    public function test_インスタンス化できる(): void
    {
        $category = new Category(
            1,
            '本'
        );

        $this->assertInstanceOf(Category::class, $category);
    }
}
