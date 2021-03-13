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
use CodeIgniter\HTTP\IncomingRequest;
use Kenjis\CI4Twig\Twig;

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

    /** @var AddToCartUseCase */
    private $addToCartUseCase;

    /** @var CartRepository */
    private $cartRepository;

    /** @var CategoryRepository */
    private $categoryRepository;

    public function __construct(
        CategoryRepository $categoryRepository,
        CartRepository $cartRepository,
        AddToCartUseCase $addToCartUseCase,
        FieldValidation $fieldValidation,
        Twig $twig
    ) {
        parent::__construct();

        $this->categoryRepository = $categoryRepository;
        $this->cartRepository = $cartRepository;
        $this->addToCartUseCase = $addToCartUseCase;

        $this->fieldValidation = $fieldValidation;
        $this->twig = $twig;
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
        $catList = $this->categoryRepository->findAll();

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
