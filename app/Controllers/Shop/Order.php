<?php

declare(strict_types=1);

/* 簡易ショッピングカート
 *
 */

namespace App\Controllers\Shop;

use App\Controllers\MyController;
use App\Models\Shop\CartModel;
use App\Models\Shop\CustomerModel;
use App\Models\Shop\InventoryModel;
use App\Models\Shop\MailModel;
use App\Models\Shop\ShopModel;
use CodeIgniter\HTTP\IncomingRequest;
use Kenjis\CI3Compatible\Core\CI_Config;
use Kenjis\CI3Compatible\Core\CI_Input;
use Kenjis\CI3Compatible\Database\CI_DB;
use Kenjis\CI3Compatible\Library\CI_Email;
use Kenjis\CI3Compatible\Library\CI_Parser;
use Kenjis\CI3Compatible\Library\CI_Session;
use Kenjis\CI4Twig\Twig;

/**
 * @property CI_Session $session
 * @property CI_Parser $parser
 * @property CI_Config $config
 * @property CI_Input $input
 * @property CI_DB $db
 */
class Order extends MyController
{
    /** @var IncomingRequest */
    protected $request;

    /** @var int 1ページに表示する商品の数 */
    private $limit;

    /** @var string 管理者のメールアドレス */
    private $admin;

    /** @var Twig */
    private $twig;

    /** @var string[] */
    protected $helpers = ['form', 'url'];

    /** @var ShopModel */
    private $shopModel;

    /** @var InventoryModel */
    private $inventoryModel;

    /** @var CartModel */
    private $cartModel;

    /** @var CustomerModel */
    private $customerModel;

    public function __construct()
    {
        parent::__construct();

        $this->load->library(['session', 'parser']);
        $this->load->database();

        $this->loadConfig();
        $this->loadDependencies();
    }

    private function loadDependencies(): void
    {
        $this->twig = new Twig();

// モデルをロードします。
        $mailModel = new MailModel(new CI_Email());
        $this->inventoryModel = new InventoryModel($this->db);
        $this->cartModel = new CartModel($this->inventoryModel, $this->session);
        $this->customerModel = new CustomerModel($this->session);
        $this->shopModel = new ShopModel(
            $this->cartModel,
            $this->customerModel,
            $mailModel,
            $this->parser
        );
    }

    private function loadConfig(): void
    {
// このアプリケーション専用の設定ファイルConfigShop.phpを読み込みます。
// load()メソッドの第2引数にTRUEを指定すると、他の設定ファイルで使われている
// 設定項目名との衝突を気にしなくても済みます。
        $this->config->load('ConfigShop', true);
// 上記のように読み込んだ場合、設定値は、以下のようにitem()メソッドに引数で
// 「設定項目名」と「設定ファイル名」を渡すことで取得できます。
        $this->admin = (string) $this->config->item('admin_email', 'ConfigShop');
    }

    /**
     * 注文処理
     */
    public function index(): string
    {
        if ($this->cartModel->count() === 0) {
            return '買い物カゴには何も入っていません。';
        }

// モデルのorder()メソッドを呼び出し、注文データの処理を依頼します。
        if ($this->shopModel->order($this->admin)) {
            // 注文が完了したので、セッションを破棄します。
            $this->session->sess_destroy();

            $data = [
                'action' => '注文の完了',
                'main'   => 'shop_thankyou',
            ];

            return $this->twig->render('shop_tmpl_checkout', $data);
        }

        return 'システムエラー';
    }
}
