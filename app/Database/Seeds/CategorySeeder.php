<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use Kenjis\CI3Compatible\Library\Seeder;

class CategorySeeder extends Seeder
{
    private $table = 'category';

    public function run(): void
    {
        $this->db->truncate($this->table);

        $data = [
            'id' => 1,
            'name' => '本',
        ];
        $this->db->insert($this->table, $data);

        $data = [
            'id' => 2,
            'name' => 'CD',
        ];
        $this->db->insert($this->table, $data);

        $data = [
            'id' => 3,
            'name' => 'DVD',
        ];
        $this->db->insert($this->table, $data);
    }
}
