<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Database\CI_DB;
use stdClass;

use function array_map;
use function explode;

class ProductRepository
{
    /** @var CI_DB */
    private $db;

    public function __construct(CI_DB $db)
    {
        $this->db = $db;
    }

    /**
     * @return Product[]
     */
    public function fiindListByCategoryId(int $catId, int $limit, int $offset): array
    {
        $this->db->where('category_id', $catId);
        $this->db->order_by('id');
        $query = $this->db->get('product', $limit, $offset);

        return array_map(
            static function (stdClass $product) {
                return new Product(
                    (int) $product->id,
                    (int) $product->category_id, // phpcs:ignore
                    $product->name,
                    $product->detail,
                    (int) $product->price,
                    (string) $product->img
                );
            },
            $query->result()
        );
    }

    public function countByCategoryId(int $catId): int
    {
        $this->db->where('category_id', $catId);
        $query = $this->db->get('product');

        return $query->num_rows();
    }

    public function findById(int $id): Product
    {
        $this->db->where('id', $id);
        $query = $this->db->get('product');

        $product = $query->row();

        return new Product(
            (int) $product->id,
            (int) $product->category_id, // phpcs:ignore
            $product->name,
            $product->detail,
            (int) $product->price,
            (string) $product->img
        );
    }

    /**
     * @return Product[]
     */
    public function findByKeyword(string $q, int $limit, int $offset): array
    {
// 検索キーワードをスペースで分割し、like()メソッドでLIKE句を指定します。
// 複数回like()メソッドを呼んだ場合は、AND条件になります。
// name LIKE '%{$keyword}%' AND name LIKE '%{$keyword}%' というSQL文になります。
        $keywords = explode(' ', $q);
        foreach ($keywords as $keyword) {
            $this->db->like('name', $keyword);
        }

        $this->db->order_by('id');
        $query = $this->db->get('product', $limit, $offset);

        return array_map(
            static function (stdClass $product) {
                return new Product(
                    (int) $product->id,
                    (int) $product->category_id, // phpcs:ignore
                    $product->name,
                    $product->detail,
                    (int) $product->price,
                    (string) $product->img
                );
            },
            $query->result()
        );
    }

    public function countBySearch(string $q): int
    {
        $this->db->select('name');
        $keywords = explode(' ', $q);
        foreach ($keywords as $keyword) {
            $this->db->like('name', $keyword);
        }

        $this->db->order_by('id');
        $query = $this->db->get('product');

        return $query->num_rows();
    }

    public function isAvailableById(int $id): bool
    {
        $this->db->where('id', $id);
        $query = $this->db->get('product');

        return $query->num_rows() === 1;
    }
}
