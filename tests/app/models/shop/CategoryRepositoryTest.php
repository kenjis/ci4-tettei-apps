<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Test\TestCase\UnitTestCase;

use function get_instance;

class CategoryRepositoryTest extends UnitTestCase
{
    /** @var CategoryRepository */
    private $categoryRepository;

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

        $this->categoryRepository = new CategoryRepository($CI->db);
    }
    // endregion

    // region Tests
    public function test_get_category_list(): void
    {
        $list = $this->categoryRepository->getCategoryList();

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
        $actual = $this->categoryRepository->getCategoryName(1);

        $expected = '本';
        $this->assertEquals($expected, $actual);
    }
    // endregion
}
