<?php

declare(strict_types=1);

namespace App\Database\Migrations;

/**
 * Migration: Create_captcha
 *
 * Created by: Cli for CodeIgniter <https://github.com/kenjis/codeigniter-cli>
 * Created on: 2015/05/12 06:00:32
 */
class Migration_Create_captcha extends CI_Migration
{
    public function up(): void
    {
        $this->dbforge->add_field([
            'captcha_id' => [
                'type' => 'BIGINT',
                'constraint' => 13,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'captcha_time' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
            ],
            'word' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],
        ]);
        $this->dbforge->add_key('captcha_id', true);
        $this->dbforge->add_key('word');
        $this->dbforge->create_table('captcha');
    }

    public function down(): void
    {
        $this->dbforge->drop_table('captcha');
    }
}
