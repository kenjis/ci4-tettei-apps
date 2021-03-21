<?php

declare(strict_types=1);

/* 簡易ショッピングカート
 *
 */

namespace App\Controllers\Shop;

use App\Libraries\GeneratePagination;
use App\Libraries\Validation\FieldValidation;
use App\Models\Shop\CartRepository;
use App\Models\Shop\CategoryRepository;
use App\Models\Shop\ProductRepository;
use Kenjis\CI3Compatible\Core\CI_Config;
use Kenjis\CI4Twig\Twig;

use function max;
use function mb_convert_kana;
use function trim;

/**
 * @property CI_Config $config
 */
class Search extends ShopController
{
    /** @var FieldValidation */
    private $fieldValidation;

    /** @var CartRepository */
    private $cartRepository;

    /** @var ProductRepository */
    private $productRepository;

    /** @var CategoryRepository */
    private $categoryRepository;

    /** @var GeneratePagination */
    private $generatePagination;

    public function __construct(
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        CartRepository $cartRepository,
        GeneratePagination $generatePagination,
        FieldValidation $fieldValidation,
        Twig $twig
    ) {
        parent::__construct();

        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->cartRepository = $cartRepository;

        $this->generatePagination = $generatePagination;
        $this->fieldValidation = $fieldValidation;
        $this->twig = $twig;
    }

    /**
     * 検索ページ
     */
    public function index(string $page = '0'): string
    {
        [$q, $offset] = $this->getParams($page);

        $catList = $this->categoryRepository->findAll();

// モデルから、キーワードで検索した商品データを取得します。
        $list = $this->productRepository->findByKeyword(
            $q,
            $this->limit,
            $offset
        );

// ページネーションを生成します。
        [$total, $pagination] = $this->createPaginationSearch($q);

        $cart = $this->cartRepository->find();

        $data = [
            'cat_list' => $catList,
            'list' => $list,
            'pagination' => $pagination,
            'q' => $q,
            'total' => $total,
            'main' => 'shop_search',
            'item_count' => $cart->getLineCount(),
        ];

        return $this->twig->render('shop_tmpl_shop', $data);
    }

    /**
     * 入力パラメータを検証・変換して返す
     *
     * @return array{0: string, 1: int}
     */
    private function getParams(string $page): array
    {
        $page = $this->convertToInt($page);

// ページ番号をoffsetに変換します。
        $offset = max($page - 1, 0) * $this->limit;

// オフセットを検証します。
        $this->fieldValidation->validate(
            $offset,
            'required|is_natural|max_length[3]'
        );

// 検索キーワードをクエリ文字列から取得します。
        $q = (string) $this->request->getGet('q');

// 全角スペースを半角スペースに変換します。
        $q = trim(mb_convert_kana($q, 's'));

// 検索キーワードを検証します。
        $this->fieldValidation->validate(
            $q,
            'max_length[100]'
        );

        return [$q, $offset];
    }

    /**
     * @return array{0: int, 1: string}
     */
    private function createPaginationSearch(string $q): array
    {
// ページネーションを生成します。
        $path  = '/shop/search';
        $total = $this->productRepository->countBySearch($q);

        $config = [
// リンク先のURLを指定します。
            'base_url' => $this->config->site_url($path),
// 1ページに表示する件数を指定します。
            'per_page' => $this->limit,
// 総件数を指定します。
            'total_rows' => $total,
// ページ番号情報がどのURIセグメントに含まれるか指定します。
            'uri_segment' => 3,
        ];
        $pagination = $this->generatePagination->getLinks($config);

        return [$total, $pagination];
    }
}
