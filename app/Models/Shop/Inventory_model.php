<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Core\CI_Model;
use Kenjis\CI3Compatible\Database\CI_DB;

use function explode;
use function show_error;

/**
 * @property CI_DB $db
 */
class Inventory_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    public function get_category_list()
    {
        $this->db->order_by('id');
        $query = $this->db->get('category');

        return $query->result();
    }

    public function get_category_name($id)
    {
        $this->db->select('name');
        $this->db->where('id', $id);
        $query = $this->db->get('category');
        $row = $query->row();

        if ($row === null) {
            show_error('不正な入力です。');
        }

        return $row->name;
    }

    public function get_product_list($cat_id, $limit, $offset)
    {
        $this->db->where('category_id', $cat_id);
        $this->db->order_by('id');
        $query = $this->db->get('product', $limit, $offset);

        return $query->result();
    }

    public function get_product_count($cat_id)
    {
        $this->db->where('category_id', $cat_id);
        $query = $this->db->get('product');

        return $query->num_rows();
    }

    public function get_product_item($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('product');

        return $query->row();
    }

    public function is_available_product_item($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('product');

        return $query->num_rows() == 1;
    }

    public function get_product_by_search($q, $limit, $offset)
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

        return $query->result();
    }

    public function get_count_by_search($q)
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
}
