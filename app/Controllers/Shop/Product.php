<?php

declare(strict_types=1);

/* 簡易ショッピングカート
 *
 */

namespace App\Controllers\Shop;

use App\Libraries\Validation\FieldValidation;
use App\Models\Shop\CartRepository;
use App\Models\Shop\CategoryRepository;
use App\Models\Shop\ProductRepository;
use Kenjis\CI4Twig\Twig;

class Product extends ShopController
{
    /** @var FieldValidation */
    private $fieldValidation;

    /** @var CartRepository */
    private $cartRepository;

    /** @var ProductRepository */
    private $productRepository;

    /** @var CategoryRepository */
    private $categoryRepository;

    public function __construct(
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        CartRepository $cartRepository,
        FieldValidation $fieldValidation,
        Twig $twig
    ) {
        parent::__construct();

        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->cartRepository = $cartRepository;

        $this->fieldValidation = $fieldValidation;
        $this->twig = $twig;
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

        $catList = $this->categoryRepository->findAll();

// モデルより商品データを取得します。
        $item = $this->productRepository->findById($prodId);

        $cart = $this->cartRepository->find();
        $itemCount = $cart->getLineCount();

        $data = [
            'cat_list' => $catList,
            'item' => $item,
            'main' => 'shop_product',
            'item_count' => $itemCount,
        ];

        return $this->twig->render('shop_tmpl_shop', $data);
    }
}
