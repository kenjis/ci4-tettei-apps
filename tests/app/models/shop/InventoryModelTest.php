<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Test\TestCase\UnitTestCase;

use function get_instance;

class InventoryModelTest extends UnitTestCase
{
    /** @var InventoryModel */
    private $inventoryModel;

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
        $this->inventoryModel = new InventoryModel($CI->db);
    }
    // endregion

    // region Tests
    public function test_get_category_list(): void
    {
        $list = $this->inventoryModel->getCategoryList();

        $expected = [
            1 => '本',
            2 => 'CD',
            3 => 'DVD',
        ];
        foreach ($list as $category) {
            $this->assertEquals($expected[$category->id], $category->name);
        }
    }

    public function test_get_category_name(): void
    {
        $actual = $this->inventoryModel->getCategoryName(1);

        $expected = '本';
        $this->assertEquals($expected, $actual);
    }

    public function test_get_product_count(): void
    {
        $actual = $this->inventoryModel->getProductCount(1);

        $expected = 36;
        $this->assertEquals($expected, $actual);
    }

    public function test_get_product_list(): void
    {
        $list = $this->inventoryModel->getProductList(1, 1, 0);

        $expected = [1 => 'CodeIgniter徹底入門'];
        foreach ($list as $product) {
            $this->assertEquals($expected[$product->id], $product->name);
        }
    }

    public function test_get_product_item(): void
    {
        $item = $this->inventoryModel->getProductItem(1);

        $expected = 'CodeIgniter徹底入門';
        $this->assertEquals($expected, $item->name);
    }

    public function test_get_product_by_search(): void
    {
        $results = $this->inventoryModel->getProductBySearch('CodeIgniter', 10, 0);

        foreach ($results as $record) {
            $this->assertStringContainsString('CodeIgniter', $record->name);
        }
    }

    public function test_get_count_by_search(): void
    {
        $actual = $this->inventoryModel->getCountBySearch('CodeIgniter');

        $expected = 3;
        $this->assertEquals($expected, $actual);
    }

    public function test_is_available_product_item_not_available(): void
    {
        $actual = $this->inventoryModel->isAvailableProductItem(9999999999);

        $this->assertFalse($actual);
    }
    // endregion
}
