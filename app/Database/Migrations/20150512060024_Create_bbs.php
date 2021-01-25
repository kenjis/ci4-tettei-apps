<?php

declare(strict_types=1);

/**
 * Migration: Create_bbs
 *
 * Created by: Cli for CodeIgniter <https://github.com/kenjis/codeigniter-cli>
 * Created on: 2015/05/12 06:00:24
 */
class Migration_Create_bbs extends CI_Migration
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
                'constraint' => '64',
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => true,
            ],
            'subject' => [
                'type' => 'VARCHAR',
                'constraint' => '128',
                'null' => true,
            ],
            'body' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => '32',
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => '39',
                'null' => true,
            ],
        ]);
        $this->dbforge->add_field(
            '`datetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        );
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('bbs');
    }

    public function down(): void
    {
        $this->dbforge->drop_table('bbs');
    }
}
