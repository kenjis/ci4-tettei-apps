<?php

declare(strict_types=1);

/* 簡易ショッピングカート
 *
 */

namespace App\Controllers\Shop;

use App\Controllers\MyController;
use App\Libraries\Validation\FieldValidation;
use App\Models\Shop\CartModel;
use App\Models\Shop\InventoryModel;
use CodeIgniter\HTTP\IncomingRequest;
use Config\Services;
use Kenjis\CI3Compatible\Core\CI_Config;
use Kenjis\CI3Compatible\Core\CI_Input;
use Kenjis\CI3Compatible\Database\CI_DB;
use Kenjis\CI3Compatible\Library\CI_Session;
use Kenjis\CI4Twig\Twig;

/**
 * @property CI_Session $session
 * @property CI_Config $config
 * @property CI_Input $input
 * @property CI_DB $db
 */
class Product extends MyController
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

    /** @var FieldValidation */
    private $fieldValidation;

    /** @var InventoryModel */
    private $inventoryModel;

    /** @var CartModel */
    private $cartModel;

    public function __construct()
    {
        parent::__construct();

        $this->load->library(['session']);
        $this->load->database();

        $this->loadDependencies();
    }

    private function loadDependencies(): void
    {
        $this->fieldValidation = new FieldValidation(Services::validation());
        $this->twig = new Twig();

// モデルをロードします。
        $this->inventoryModel = new InventoryModel($this->db);
        $this->cartModel = new CartModel($this->inventoryModel, $this->session);
    }

    /**
     * 商品詳細ページ
     */
    public function index(string $prodId = '1'): string
    {
        $prodId = $this->convertToInt($prodId);

// 商品IDを検証します。
        $this->fieldValidation->validate(
            $prodId,
            'required|is_natural|max_length[11]'
        );

        $catList = $this->inventoryModel->getCategoryList();

// モデルより商品データを取得します。
        $item = $this->inventoryModel->getProductItem($prodId);

        $itemCount = $this->cartModel->count();

        $data = [
            'cat_list' => $catList,
            'item' => $item,
            'main' => 'shop_product',
            'item_count' => $itemCount,
        ];

        return $this->twig->render('shop_tmpl_shop', $data);
    }
}
