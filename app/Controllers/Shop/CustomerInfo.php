<?php

declare(strict_types=1);

/* 簡易ショッピングカート
 *
 */

namespace App\Controllers\Shop;

use App\Libraries\Validation\FormValidation;
use App\Models\Shop\CartRepository;
use App\Models\Shop\CustomerInfoForm;
use App\Models\Shop\CustomerInfoRepository;
use Kenjis\CI4\AttributeRoutes\Route;
use Kenjis\CI4Twig\Twig;

class CustomerInfo extends ShopController
{
    /** @var CustomerInfoRepository */
    private $customerInfoRepository;

    /** @var CartRepository */
    private $cartRepository;

    /** @var FormValidation */
    private $formValidation;

    public function __construct(
        CartRepository $cartRepository,
        CustomerInfoRepository $customerInfoRepository,
        FormValidation $formValidation,
        Twig $twig
    ) {
        parent::__construct();

        $this->cartRepository = $cartRepository;
        $this->customerInfoRepository = $customerInfoRepository;

        $this->formValidation = $formValidation;
        $this->twig = $twig;
    }

    /**
     * お客様情報入力ページ
     */
    #[Route('shop/customer_info', methods: ['post'])]
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
    #[Route('shop/confirm', methods: ['post'])]
    public function confirm(): string
    {
        $this->postOnly();

        $customerInfo = new CustomerInfoForm();

        $isValid = $this->formValidation->validate($this->request, $customerInfo);
        if (! $isValid) {
            $data = [
                'action' => 'お客様情報の入力',
                'main' => 'shop_customer_info',
            ];

            return $this->twig->render('shop_tmpl_checkout', $data);
        }

// 検証をパスした入力データは、モデルを使って保存します。
        $this->customerInfoRepository->save($customerInfo);

        $cart = $this->cartRepository->find();

        $data = [
            'name' => $customerInfo['name'],
            'zip' => $customerInfo['zip'],
            'addr' => $customerInfo['addr'],
            'tel' => $customerInfo['tel'],
            'email' => $customerInfo['email'],
            'total' => $cart->getTotal(),
            'cart' => $cart->getItems(),
            'action' => '注文内容の確認',
            'main' => 'shop_confirm',
        ];

        return $this->twig->render('shop_tmpl_checkout', $data);
    }
}
