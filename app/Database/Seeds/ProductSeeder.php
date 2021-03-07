<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use Faker\Factory;
use Kenjis\CI3Compatible\Library\Seeder;

class ProductSeeder extends Seeder
{
    /** @var string */
    private $table = 'product';

    public function run(): void
    {
        $this->db_->truncate($this->table);

        $data = [
            'category_id' => 1,
            'name'   => 'CodeIgniter徹底入門',
            'detail' => '日本初のCodeIgniter解説書。CodeIgniterのインストールや運用法、開発の基礎知識を紹介するとともに、主なライブラリの使い方や活用法、応用テクニックなどを具体的なサンプルプログラムを交えて徹底的に解説している。PHPフレームワーク導入を検討しているWeb開発者、また、他のPHPフレームワークが難しいと感じているユーザーにお勧めの1冊。',
            'price'  => 3800,
        ];
        $this->db_->insert($this->table, $data);

        $data = [
            'category_id' => 2,
            'name'   => 'CodeIgniter徹底入門 CD',
            'detail' => 'CodeIgniter徹底入門 CD',
            'price'  => 3800,
        ];
        $this->db_->insert($this->table, $data);

        $data = [
            'category_id' => 3,
            'name'   => 'CodeIgniter徹底入門 DVD',
            'detail' => 'CodeIgniter徹底入門 DVD',
            'price'  => 3800,
        ];
        $this->db_->insert($this->table, $data);

        $faker = Factory::create('ja_JP');
        $catId = 1;
        for ($i = 0; $i < 100; $i++) {
            $data = [
                'category_id' => $catId,
                'name'   => $faker->country,
                'detail' => $faker->text,
                'price'  => $faker->numberBetween(1000, 5000),
            ];

            $this->db_->insert($this->table, $data);

            if ($i === 34) {
                $catId++;
            } elseif ($i === 67) {
                $catId++;
            }
        }
    }
}
