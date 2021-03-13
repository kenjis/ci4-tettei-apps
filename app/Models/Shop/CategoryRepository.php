<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Database\CI_DB;
use Kenjis\CI3Compatible\Exception\RuntimeException;
use stdClass;

use function array_map;

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

        if ($row === null) {
            throw new RuntimeException('不正な入力です。');
        }

        return $row->name;
    }
}
