<?php

declare(strict_types=1);

/* 簡易ショッピングカート
 *
 */

namespace App\Controllers\Shop;

use App\Models\Shop\CartRepository;
use App\Models\Shop\OrderUseCase;
use Kenjis\CI3Compatible\Core\CI_Config;
use Kenjis\CI3Compatible\Library\CI_Session;
use Kenjis\CI4\AttributeRoutes\Route;
use Kenjis\CI4Twig\Twig;

/**
 * @property CI_Config $config
 */
class Order extends ShopController
{
    /** @var OrderUseCase */
    private $orderUseCase;

    /** @var CartRepository */
    private $cartRepository;

    /** @var CI_Session */
    private $session;

    public function __construct(
        CartRepository $cartRepository,
        OrderUseCase $orderUseCase,
        Twig $twig,
        CI_Session $session
    ) {
        parent::__construct();

        $this->cartRepository = $cartRepository;
        $this->orderUseCase = $orderUseCase;

        $this->twig = $twig;
        $this->session = $session;
    }

    /**
     * 注文処理
     */
    #[Route('shop/order', methods: ['post'])]
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
