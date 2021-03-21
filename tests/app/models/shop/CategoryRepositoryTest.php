<?php

declare(strict_types=1);

namespace App\Models\Shop;

use App\Exception\RuntimeException;
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
    public function test_カテゴリのリストを取得できる(): void
    {
        $list = $this->categoryRepository->findAll();

        $expected = [
            1 => '本',
            2 => 'CD',
            3 => 'DVD',
        ];
        foreach ($list as $category) {
            $this->assertEquals($expected[$category->id], $category->name);
        }
    }

    public function test_カテゴリIDからカテゴリ名を取得できる(): void
    {
        $actual = $this->categoryRepository->findNameById(1);

        $expected = '本';
        $this->assertEquals($expected, $actual);
    }

    public function test_存在しないカテゴリIDには例外が返る(): void
    {
        $this->expectException(RuntimeException::class);

        $this->categoryRepository->findNameById(0);
    }
    // endregion
}
