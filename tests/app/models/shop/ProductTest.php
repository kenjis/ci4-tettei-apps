<?php

declare(strict_types=1);

namespace App\Models\Shop;

use CodeIgniter\Test\CIUnitTestCase;

class ProductTest extends CIUnitTestCase
{
    public function test_インスタンス化できる(): void
    {
        $pro = new Product(
            1,
            1,
            'CodeIgniter徹底入門',
            '日本初のCodeIgniter解説書。',
            3800,
            'codeigniter-tettei.png',
        );

        $this->assertInstanceOf(Product::class, $pro);
    }
}
