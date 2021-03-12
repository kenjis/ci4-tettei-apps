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
class Cart extends MyController
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
     * 買い物かごに入れる
     */
    public function add(string $prodId = '0'): string
    {
// $prod_idの型をintに変更します。
        $prodId = $this->convertToInt($prodId);

// POSTされたqtyフィールドより、数量を取得します。
        $qty = (int) $this->request->getPost('qty');

// 商品IDを検証します。
        $this->fieldValidation->validate(
            $prodId,
            'required|is_natural|max_length[11]'
        );

// 数量を検証します。
        $this->fieldValidation->validate(
            $qty,
            'required|is_natural|max_length[3]'
        );

        $this->cartModel->add($prodId, $qty);

// コントローラのcart()メソッドを呼び出し、買い物かごを表示します。
        return $this->index();
    }

    /**
     * 買い物かごページ
     */
    public function index(): string
    {
        $catList = $this->inventoryModel->getCategoryList();

// モデルより、買い物かごの情報を取得します。
        $cart = $this->cartModel->getAll();

        $data = [
            'cat_list' => $catList,
            'total' => $cart['total'],
            'cart' => $cart['items'],
            'item_count' => $cart['line'],
            'main' => 'shop_cart',
        ];

        return $this->twig->render('shop_tmpl_shop', $data);
    }
}
