<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Test\TestCase\UnitTestCase;

use function get_instance;

class ProductRepositoryTest extends UnitTestCase
{
    /** @var ProductRepository */
    private $productRepository;

    // region Fixture
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $CI =& get_instance();
        $CI->load->library('seeder');
        $CI->seeder->call('CategorySeeder');
    }

    public function setUp(): void
    {
        parent::setUp();

        $CI =& get_instance();
        $CI->load->database();

        $this->productRepository = new ProductRepository($CI->db);
    }
    // endregion

    // region Tests
    public function test_カテゴリIDで商品数を取得できる(): void
    {
        $actual = $this->productRepository->countByCategoryId(1);

        $expected = 36;
        $this->assertEquals($expected, $actual);
    }

    public function test_カテゴリIDで商品リストを取得できる(): void
    {
        $list = $this->productRepository->findListByCategoryId(1, 1, 0);

        $expected = [1 => 'CodeIgniter徹底入門'];
        foreach ($list as $product) {
            $this->assertEquals($expected[$product->id], $product->name);
        }
    }

    public function test_商品IDで商品を取得できる(): void
    {
        $item = $this->productRepository->findById(1);

        $expected = 'CodeIgniter徹底入門';
        $this->assertEquals($expected, $item->name);
    }

    public function test_キーワードで商品を検索できる(): void
    {
        $results = $this->productRepository->findByKeyword('CodeIgniter', 10, 0);

        foreach ($results as $record) {
            $this->assertStringContainsString('CodeIgniter', $record->name);
        }
    }

    public function test_キーワードで商品数を取得できる(): void
    {
        $actual = $this->productRepository->countByKeyword('CodeIgniter');

        $expected = 3;
        $this->assertEquals($expected, $actual);
    }

    public function test_存在しない商品IDの場合はfalseが返る(): void
    {
        $actual = $this->productRepository->isAvailableById(9999999999);

        $this->assertFalse($actual);
    }
    // endregion
}
