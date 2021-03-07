<?php

declare(strict_types=1);

/* 簡易ショッピングカート
 *
 */

namespace App\Controllers;

use App\Libraries\Generate_pagination;
use App\Libraries\Validation\Field_validation;
use App\Libraries\Validation\Shop_validation_customer;
use App\Models\Shop\Cart_model;
use App\Models\Shop\Customer_model;
use App\Models\Shop\Inventory_model;
use App\Models\Shop\Shop_model;
use Kenjis\CI3Compatible\Core\CI_Config;
use Kenjis\CI3Compatible\Core\CI_Input;
use Kenjis\CI3Compatible\Library\CI_Form_validation;
use Kenjis\CI3Compatible\Library\CI_Session;
use Kenjis\CI4Twig\Twig;

use function filter_var;
use function max;
use function mb_convert_kana;
use function trim;

use const FILTER_VALIDATE_INT;

/**
 * @property Shop_model $shop_model
 * @property Inventory_model $inventory_model
 * @property Cart_model $cart_model
 * @property Customer_model $customer_model
 * @property Field_validation $field_validation
 * @property Shop_validation_customer $shop_validation_customer
 * @property Generate_pagination $generate_pagination
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property CI_Config $config
 * @property CI_Input $input
 */
class Shop extends MY_Controller
{
    /** @var int 1ページに表示する商品の数 */
    public $limit;

    /** @var string 管理者のメールアドレス */
    public $admin;

    /** @var Twig */
    private $twig;

    public function __construct()
    {
        parent::__construct();

        $this->load->library([
            'validation/field_validation',
            'session',
        ]);
        $this->twig = new Twig();
        $this->load->helper(['form', 'url']);

// モデルをロードします。ロード後のモデルオブジェクトは、$this->shop_modelなど
// として利用できます。
        $this->load->model([
            'shop/shop_model',
            'shop/inventory_model',
            'shop/cart_model',
            'shop/customer_model',
        ]);

// このアプリケーション専用の設定ファイルConfigShop.phpを読み込みます。
// load()メソッドの第2引数にTRUEを指定すると、他の設定ファイルで使われている
// 設定項目名との衝突を気にしなくても済みます。
        $this->config->load('ConfigShop', true);
// 上記のように読み込んだ場合、設定値は、以下のようにitem()メソッドに引数で
// 「設定項目名」と「設定ファイル名」を渡すことで取得できます。
        $this->limit = (int) $this->config->item('per_page', 'ConfigShop');
        $this->admin = (string) $this->config->item('admin_email', 'ConfigShop');
    }

    /**
     * トップページ = カテゴリ別商品一覧
     */
    public function index(string $catId = '1', string $page = '0'): void
    {
        $catId = filter_var($catId, FILTER_VALIDATE_INT);
        $page = filter_var($page, FILTER_VALIDATE_INT);

        if ($catId === false || $page === false) {
            show_error('不正な入力です。');

            return;
        }

// ページ番号をoffsetに変換します。
        $offset = max($page - 1, 0) * $this->limit;

// カテゴリーIDとオフセットを検証します。
        $this->field_validation->validate(
            $catId,
            'required|is_natural|max_length[11]'
        );
        $this->field_validation->validate(
            $offset,
            'required|is_natural|max_length[3]'
        );

// モデルからカテゴリの一覧を取得します。
        $catList = $this->inventory_model->get_category_list();

// カテゴリIDとoffset値と、1ページに表示する商品の数を渡し、モデルより
// 商品一覧を取得します。
        $list = $this->inventory_model->get_product_list(
            $catId,
            $this->limit,
            $offset
        );
// カテゴリIDより、カテゴリ名を取得します。
        $category = $this->inventory_model->get_category_name($catId);

// ページネーションを生成します。
        [$total, $pagination] = $this->createPaginationCategory($catId);

// モデルよりカートの中の商品アイテム数を取得します。
        $itemCount = $this->cart_model->count();

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
        $this->twig->display('shop_tmpl_shop', $data);
    }

    /**
     * @return array{0: int, 1: string}
     */
    private function createPaginationCategory(int $catId): array
    {
        $this->load->library('generate_pagination');

// モデルよりそのカテゴリの商品数を取得し、ページネーションを生成します。
        $path  = '/shop/index/' . $catId;
        $total = $this->inventory_model->get_product_count($catId);

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
        $pagination = $this->generate_pagination->get_links($config);

        return [$total, $pagination];
    }

    /**
     * 商品詳細ページ
     */
    public function product(string $prodId = '1'): void
    {
        $prodId = filter_var($prodId, FILTER_VALIDATE_INT);

        if ($prodId === false) {
            show_error('不正な入力です。');

            return;
        }

// 商品IDを検証します。
        $this->field_validation->validate(
            $prodId,
            'required|is_natural|max_length[11]'
        );

        $catList = $this->inventory_model->get_category_list();

// モデルより商品データを取得します。
        $item = $this->inventory_model->get_product_item($prodId);

        $itemCount = $this->cart_model->count();

        $data = [
            'cat_list' => $catList,
            'item' => $item,
            'main' => 'shop_product',
            'item_count' => $itemCount,
        ];

        $this->twig->display('shop_tmpl_shop', $data);
    }

    /**
     * カゴに入れる
     */
    public function add(string $prodId = '0'): void
    {
// $prod_idの型をintに変更します。
        $prodId = filter_var($prodId, FILTER_VALIDATE_INT);

        if ($prodId === false) {
            show_error('不正な入力です。');

            return;
        }

// POSTされたqtyフィールドより、数量を取得します。
        $qty = (int) $this->input->post('qty');

// 商品IDを検証します。
        $this->field_validation->validate(
            $prodId,
            'required|is_natural|max_length[11]'
        );

// 数量を検証します。
        $this->field_validation->validate(
            $qty,
            'required|is_natural|max_length[3]'
        );

        $this->cart_model->add($prodId, $qty);

// コントローラのcart()メソッドを呼び出し、カートを表示します。
        $this->cart();
    }

    /**
     * 買い物カゴページ
     */
    public function cart(): void
    {
        $catList = $this->inventory_model->get_category_list();

// モデルより、カートの情報を取得します。
        $cart = $this->cart_model->get_all();

        $data = [
            'cat_list' => $catList,
            'total' => $cart['total'],
            'cart' => $cart['items'],
            'item_count' => $cart['line'],
            'main' => 'shop_cart',
        ];

        $this->twig->display('shop_tmpl_shop', $data);
    }

    /**
     * 検索ページ
     */
    public function search(string $page = '0'): void
    {
        $page = filter_var($page, FILTER_VALIDATE_INT);

// ページ番号をoffsetに変換します。
        $offset = max($page - 1, 0) * $this->limit;

// オフセットを検証します。
        $this->field_validation->validate(
            $offset,
            'required|is_natural|max_length[3]'
        );

// 検索キーワードをクエリ文字列から取得します。
        $q = (string) $this->input->get('q');
// 全角スペースを半角スペースに変換します。
        $q = trim(mb_convert_kana($q, 's'));
// 検索キーワードを検証します。
        $this->field_validation->validate(
            $q,
            'max_length[100]'
        );

        $catList = $this->inventory_model->get_category_list();

// モデルから、キーワードで検索した商品データを取得します。
        $list = $this->inventory_model->get_product_by_search(
            $q,
            $this->limit,
            $offset
        );

// ページネーションを生成します。
        [$total, $pagination] = $this->createPaginationSearch($q);

        $data = [
            'cat_list' => $catList,
            'list' => $list,
            'pagination' => $pagination,
            'q' => $q,
            'total' => $total,
            'main' => 'shop_search',
            'item_count' => $this->cart_model->count(),
        ];

        $this->twig->display('shop_tmpl_shop', $data);
    }

    /**
     * @return array{0: int, 1: string}
     */
    private function createPaginationSearch(string $q): array
    {
        $this->load->library('generate_pagination');

// ページネーションを生成します。
        $path  = '/shop/search';
        $total = $this->inventory_model->get_count_by_search($q);

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
        $pagination = $this->generate_pagination->get_links($config);

        return [$total, $pagination];
    }

    /**
     * お客様情報入力ページ
     */
    public function customer_info(): void
    {
// 検証ルールを設定します。
        $this->load->library('validation/shop_validation_customer');

        $this->shop_validation_customer->run();

        $data = [
            'action' => 'お客様情報の入力',
            'main'   => 'shop_customer_info',
        ];
        $this->twig->display('shop_tmpl_checkout', $data);
    }

    /**
     * 注文内容確認
     */
    public function confirm(): void
    {
        $this->load->library('validation/shop_validation_customer');

        if (! $this->shop_validation_customer->run()) {
            $data = [
                'action' => 'お客様情報の入力',
                'main' => 'shop_customer_info',
            ];
            $this->twig->display('shop_tmpl_checkout', $data);

            return;
        }

// 検証をパスした入力データは、モデルを使って保存します。
        $customerData = [
            'name'  => $this->input->post('name'),
            'zip'   => $this->input->post('zip'),
            'addr'  => $this->input->post('addr'),
            'tel'   => $this->input->post('tel'),
            'email' => $this->input->post('email'),
        ];
        $this->customer_model->set($customerData);

        $cart = $this->cart_model->get_all();

        $data = [
            'name' => $customerData['name'],
            'zip' => $customerData['zip'],
            'addr' => $customerData['addr'],
            'tel' => $customerData['tel'],
            'email' => $customerData['email'],
            'total' => $cart['total'],
            'cart' => $cart['items'],
            'action' => '注文内容の確認',
            'main' => 'shop_confirm',
        ];

        $this->twig->display('shop_tmpl_checkout', $data);
    }

    /**
     * 注文処理
     */
    public function order(): void
    {
        if ($this->cart_model->count() === 0) {
            echo '買い物カゴには何も入っていません。';

            return;
        }

// モデルのorder()メソッドを呼び出し、注文データの処理を依頼します。
        if ($this->shop_model->order($this->admin)) {
            $data = [
                'action' => '注文の完了',
                'main'   => 'shop_thankyou',
            ];
            $this->twig->display('shop_tmpl_checkout', $data);
// 注文が完了したので、セッションを破棄します。
            $this->session->sess_destroy();

            return;
        }

        echo 'システムエラー';
    }
}
