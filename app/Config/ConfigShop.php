<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/*
 * 簡易ショッピングカートの設定項目
 */
class ConfigShop extends BaseConfig
{
    /**
     * @var int 1ページに表示する商品の数
     */
    public $per_page  = 5;

    /**
     * @var string 管理者のメールアドレス
     */
    public $admin_email = 'admin@example.jp';
}
