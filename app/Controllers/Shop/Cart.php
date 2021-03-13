<?php

declare(strict_types=1);

/* 簡易ショッピングカート
 *
 */

namespace App\Controllers\Shop;

use App\Controllers\MyController;
use App\Libraries\Validation\FieldValidation;
use App\Models\Shop\AddToCartUseCase;
use App\Models\Shop\CartRepository;
use App\Models\Shop\CategoryRepository;
use App\Models\Shop\ProductRepository;
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

    /** @var Twig */
    private $twig;

    /** @var string[] */
    protected $helpers = ['form', 'url'];

    /** @var FieldValidation */
    private $fieldValidation;

    /** @var ProductRepository */
    private $productRepository;

    /** @var AddToCartUseCase */
    private $addToCartUseCase;

    /** @var CartRepository */
    private $cartRepository;

    /** @var CategoryRepository */
    private $categoryRepository;

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
        $this->productRepository = new ProductRepository($this->db);
        $this->categoryRepository = new CategoryRepository($this->db);
        $this->cartRepository = new CartRepository($this->session);
        $this->addToCartUseCase = new AddToCartUseCase(
            $this->cartRepository,
            $this->productRepository
        );
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

        $this->addToCartUseCase->add($prodId, $qty);

// コントローラのindex()メソッドを呼び出し、買い物かごを表示します。
        return $this->index();
    }

    /**
     * 買い物かごページ
     */
    public function index(): string
    {
        $catList = $this->categoryRepository->getCategoryList();

// モデルより、買い物かごの情報を取得します。
        $cart = $this->cartRepository->find();

        $data = [
            'cat_list' => $catList,
            'total' => $cart->getTotal(),
            'cart' => $cart->getItems(),
            'item_count' => $cart->getLineCount(),
            'main' => 'shop_cart',
        ];

        return $this->twig->render('shop_tmpl_shop', $data);
    }
}
