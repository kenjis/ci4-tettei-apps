<?php

declare(strict_types=1);

/**
 * Migration: Create_category
 *
 * Created by: Cli for CodeIgniter <https://github.com/kenjis/codeigniter-cli>
 * Created on: 2015/05/12 06:00:38
 */
class Migration_Create_category extends CI_Migration
{
    public function up(): void
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 64,
            ],
        ]);
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('category');

        $data = [
            ['name' => '本'],
            ['name' => 'CD'],
            ['name' => 'DVD'],
        ];
        $this->db->insert_batch('category', $data);
    }

    public function down(): void
    {
        $this->dbforge->drop_table('category');
    }
}
