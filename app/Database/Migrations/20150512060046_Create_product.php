<?php

declare(strict_types=1);

namespace App\Database\Migrations;

/**
 * Migration: Create_product
 *
 * Created by: Cli for CodeIgniter <https://github.com/kenjis/codeigniter-cli>
 * Created on: 2015/05/12 06:00:46
 */
class CreateProduct extends CI_Migration
{
    public function up(): void
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'category_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
            ],
            'detail' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'price' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'img' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
                'null' => true,
            ],
        ]);
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('product');
    }

    public function down(): void
    {
        $this->dbforge->drop_table('product');
    }
}
