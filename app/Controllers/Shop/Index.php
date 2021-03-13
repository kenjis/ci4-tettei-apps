<?php

declare(strict_types=1);

/* 簡易ショッピングカート
 *
 */

namespace App\Controllers\Shop;

use App\Controllers\MyController;
use App\Libraries\GeneratePagination;
use App\Libraries\Validation\FieldValidation;
use App\Models\Shop\CartRepository;
use App\Models\Shop\CategoryRepository;
use App\Models\Shop\ProductRepository;
use CodeIgniter\HTTP\IncomingRequest;
use Kenjis\CI3Compatible\Core\CI_Config;
use Kenjis\CI4Twig\Twig;

use function max;

/**
 * @property CI_Config $config
 */
class Index extends MyController
{
    /** @var IncomingRequest */
    protected $request;

    /** @var int 1ページに表示する商品の数 */
    private $limit;

    /** @var Twig */
    private $twig;

    /** @var string[] */
    protected $helpers = ['form', 'url'];

    /** @var FieldValidation */
    private $fieldValidation;

    /** @var CartRepository */
    private $cartRepository;

    /** @var CategoryRepository */
    private $categoryRepository;

    /** @var ProductRepository */
    private $productRepository;

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

        $this->loadConfig();

        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
        $this->cartRepository = $cartRepository;

        $this->generatePagination = $generatePagination;
        $this->fieldValidation = $fieldValidation;
        $this->twig = $twig;
    }

    private function loadConfig(): void
    {
// このアプリケーション専用の設定ファイルConfigShop.phpを読み込みます。
// load()メソッドの第2引数にTRUEを指定すると、他の設定ファイルで使われている
// 設定項目名との衝突を気にしなくても済みます。
        $this->config->load('ConfigShop', true);
// 上記のように読み込んだ場合、設定値は、以下のようにitem()メソッドに引数で
// 「設定項目名」と「設定ファイル名」を渡すことで取得できます。
        $this->limit = (int) $this->config->item('per_page', 'ConfigShop');
    }

    /**
     * トップページ = カテゴリ別商品一覧
     */
    public function index(string $catId = '1', string $page = '0'): string
    {
        $catId = $this->convertToInt($catId);
        $page = $this->convertToInt($page);

// ページ番号をoffsetに変換します。
        $offset = max($page - 1, 0) * $this->limit;

// カテゴリーIDとオフセットを検証します。
        $this->fieldValidation->validate(
            $catId,
            'required|is_natural|max_length[11]'
        );
        $this->fieldValidation->validate(
            $offset,
            'required|is_natural|max_length[3]'
        );

// モデルからカテゴリの一覧を取得します。
        $catList = $this->categoryRepository->findAll();

// カテゴリIDとoffset値と、1ページに表示する商品の数を渡し、モデルより
// 商品一覧を取得します。
        $list = $this->productRepository->fiindListByCategoryId(
            $catId,
            $this->limit,
            $offset
        );
// カテゴリIDより、カテゴリ名を取得します。
        $category = $this->categoryRepository->findNameById($catId);

// ページネーションを生成します。
        [$total, $pagination] = $this->createPaginationCategory($catId);

// モデルよりカートの中の商品アイテム数を取得します。
        $cart = $this->cartRepository->find();
        $itemCount = $cart->getLineCount();

        $data = [
            'cat_list' => $catList,
            'list' => $list,
            'category' => $category,
            'pagination' => $pagination,
            'total' => $total,
            'main' => 'shop_list',
            'item_count' => $itemCount,
        ];

// ビューを表示します。
        return $this->twig->render('shop_tmpl_shop', $data);
    }

    /**
     * @return array{0: int, 1: string}
     */
    private function createPaginationCategory(int $catId): array
    {
// モデルよりそのカテゴリの商品数を取得し、ページネーションを生成します。
        $path  = '/shop/index/' . $catId;
        $total = $this->productRepository->countByCategoryId($catId);

        $config = [
// リンク先のURLを指定します。
            'base_url' => $this->config->site_url($path),
// 1ページに表示する件数を指定します。
            'per_page' => $this->limit,
// 総件数を指定します。
            'total_rows' => $total,
// ページ番号情報がどのURIセグメントに含まれるか指定します。
            'uri_segment' => 4,
        ];
        $pagination = $this->generatePagination->getLinks($config);

        return [$total, $pagination];
    }
}
