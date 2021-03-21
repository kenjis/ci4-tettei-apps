<?php

declare(strict_types=1);

/* 簡易ショッピングカート
 *
 */

namespace App\Controllers\Shop;

use App\Controllers\MyController;
use CodeIgniter\HTTP\IncomingRequest;
use Kenjis\CI3Compatible\Core\CI_Config;
use Kenjis\CI4Twig\Twig;

/**
 * @property CI_Config $config
 */
abstract class ShopController extends MyController
{
    /** @var IncomingRequest */
    protected $request;

    /** @var string 管理者のメールアドレス */
    protected $admin;

    /** @var int 1ページに表示する商品の数 */
    protected $limit;

    /** @var Twig */
    protected $twig;

    /** @var string[] */
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
        parent::__construct();

        $this->loadConfig();
    }

    protected function loadConfig(): void
    {
// このアプリケーション専用の設定ファイルConfigShop.phpを読み込みます。
// load()メソッドの第2引数にTRUEを指定すると、他の設定ファイルで使われている
// 設定項目名との衝突を気にしなくても済みます。
        $this->config->load('ConfigShop', true);

// 上記のように読み込んだ場合、設定値は、以下のようにitem()メソッドに引数で
// 「設定項目名」と「設定ファイル名」を渡すことで取得できます。
        $this->admin = (string) $this->config->item('admin_email', 'ConfigShop');
        $this->limit = (int) $this->config->item('per_page', 'ConfigShop');
    }
}
