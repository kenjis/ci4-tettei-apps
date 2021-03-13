<?php

declare(strict_types=1);

/* 簡易ショッピングカート
 *
 */

namespace App\Controllers\Shop;

use App\Controllers\MyController;
use App\Models\Shop\CartRepository;
use App\Models\Shop\CustomerInfoRepository;
use App\Models\Shop\OrderUseCase;
use CodeIgniter\HTTP\IncomingRequest;
use Kenjis\CI3Compatible\Core\CI_Config;
use Kenjis\CI3Compatible\Library\CI_Session;
use Kenjis\CI4Twig\Twig;

/**
 * @property CI_Config $config
 */
class Order extends MyController
{
    /** @var IncomingRequest */
    protected $request;

    /** @var string 管理者のメールアドレス */
    private $admin;

    /** @var Twig */
    private $twig;

    /** @var string[] */
    protected $helpers = ['form', 'url'];

    /** @var OrderUseCase */
    private $orderUseCase;

    /** @var CustomerInfoRepository */
    private $customerInfoRepository;

    /** @var CartRepository */
    private $cartRepository;

    /** @var CI_Session */
    private $session;

    public function __construct(
        CartRepository $cartRepository,
        CustomerInfoRepository $customerInfoRepository,
        OrderUseCase $orderUseCase,
        Twig $twig,
        CI_Session $session
    ) {
        parent::__construct();

        $this->loadConfig();

        $this->cartRepository = $cartRepository;
        $this->customerInfoRepository = $customerInfoRepository;
        $this->orderUseCase = $orderUseCase;

        $this->twig = $twig;
        $this->session = $session;
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
        $cart = $this->cartRepository->find();

        if ($cart->getLineCount() === 0) {
            return '買い物カゴには何も入っていません。';
        }

// モデルのorder()メソッドを呼び出し、注文データの処理を依頼します。
        if ($this->orderUseCase->order($this->admin)) {
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
