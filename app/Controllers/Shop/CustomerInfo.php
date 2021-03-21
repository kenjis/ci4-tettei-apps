<?php

declare(strict_types=1);

/* 簡易ショッピングカート
 *
 */

namespace App\Controllers\Shop;

use App\Models\Shop\CartRepository;
use App\Models\Shop\CustomerInfoForm;
use App\Models\Shop\CustomerInfoRepository;
use Kenjis\CI4Twig\Twig;

class CustomerInfo extends ShopController
{
    /** @var CustomerInfoForm */
    private $customerInfo;

    /** @var CustomerInfoRepository */
    private $customerInfoRepository;

    /** @var CartRepository */
    private $cartRepository;

    public function __construct(
        CartRepository $cartRepository,
        CustomerInfoRepository $customerInfoRepository,
        Twig $twig
    ) {
        parent::__construct();

        $this->cartRepository = $cartRepository;
        $this->customerInfoRepository = $customerInfoRepository;

        $this->twig = $twig;
    }

    /**
     * お客様情報入力ページ
     */
    public function index(): string
    {
        $data = [
            'action' => 'お客様情報の入力',
            'main'   => 'shop_customer_info',
        ];

        return $this->twig->render('shop_tmpl_checkout', $data);
    }

    /**
     * 注文内容確認
     */
    public function confirm(): string
    {
        $this->postOnly();

        $this->customerInfo = new CustomerInfoForm();

        if (! $this->validate($this->customerInfo->getValidationRules())) {
            $data = [
                'action' => 'お客様情報の入力',
                'main' => 'shop_customer_info',
            ];

            return $this->twig->render('shop_tmpl_checkout', $data);
        }

// 検証をパスした入力データは、モデルを使って保存します。
        $this->customerInfo->setData($this->request->getPost());
        $this->customerInfoRepository->save($this->customerInfo);

        $cart = $this->cartRepository->find();

        $data = [
            'name' => $this->customerInfo['name'],
            'zip' => $this->customerInfo['zip'],
            'addr' => $this->customerInfo['addr'],
            'tel' => $this->customerInfo['tel'],
            'email' => $this->customerInfo['email'],
            'total' => $cart->getTotal(),
            'cart' => $cart->getItems(),
            'action' => '注文内容の確認',
            'main' => 'shop_confirm',
        ];

        return $this->twig->render('shop_tmpl_checkout', $data);
    }
}
