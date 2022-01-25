<?php

declare(strict_types=1);

namespace App\Models\Shop;

use App\Exception\RuntimeException;
use Kenjis\CI3Compatible\Database\CI_DB;
use stdClass;

use function array_map;
use function assert;

class CategoryRepository
{
    /** @var CI_DB */
    private $db;

    public function __construct(CI_DB $db)
    {
        $this->db = $db;
    }

    /**
     * @return Category[]
     */
    public function findAll(): array
    {
        $this->db->order_by('id');
        $query = $this->db->get('category');

        return array_map(
            static function (stdClass $category) {
                return new Category(
                    (int) $category->id,
                    $category->name
                );
            },
            $query->result()
        );
    }

    public function findNameById(int $id): string
    {
        $this->db->select('name');
        $this->db->where('id', $id);
        $query = $this->db->get('category');

        $row = $query->row();
        assert($row instanceof stdClass || $row === null);

        if ($row === null) {
            throw new RuntimeException('不正な入力です。');
        }

        return $row->name;
    }
}
