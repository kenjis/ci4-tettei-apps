<?php

declare(strict_types=1);

/* 簡易ショッピングカート
 *
 */

namespace App\Controllers\Shop;

use App\Controllers\MyController;
use App\Models\Shop\CartModel;
use App\Models\Shop\CustomerInfoForm;
use App\Models\Shop\CustomerModel;
use App\Models\Shop\InventoryModel;
use CodeIgniter\HTTP\IncomingRequest;
use Kenjis\CI3Compatible\Core\CI_Config;
use Kenjis\CI3Compatible\Core\CI_Input;
use Kenjis\CI3Compatible\Database\CI_DB;
use Kenjis\CI3Compatible\Exception\RuntimeException;
use Kenjis\CI3Compatible\Library\CI_Session;
use Kenjis\CI4Twig\Twig;

/**
 * @property CI_Session $session
 * @property CI_Config $config
 * @property CI_Input $input
 * @property CI_DB $db
 */
class CustomerInfo extends MyController
{
    /** @var IncomingRequest */
    protected $request;

    /** @var Twig */
    private $twig;

    /** @var string[] */
    protected $helpers = ['form', 'url'];

    /** @var CustomerInfoForm */
    private $customerInfo;

    /** @var InventoryModel */
    private $inventoryModel;

    /** @var CartModel */
    private $cartModel;

    /** @var CustomerModel */
    private $customerModel;

    public function __construct()
    {
        parent::__construct();

        $this->load->library(['session']);
        $this->load->database();

        $this->loadDependencies();
    }

    private function loadDependencies(): void
    {
        $this->twig = new Twig();

// モデルをロードします。
        $this->inventoryModel = new InventoryModel($this->db);
        $this->cartModel = new CartModel($this->inventoryModel, $this->session);
        $this->customerModel = new CustomerModel($this->session);
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
        if ($this->request->getMethod() !== 'post') {
            throw new RuntimeException('不正な入力です。', 400);
        }

        $this->customerInfo = new CustomerInfoForm();

        if (! $this->validate($this->customerInfo->getValidationRules())) {
            $data = [
                'action' => 'お客様情報の入力',
                'main' => 'shop_customer_info',
            ];

            return $this->twig->render('shop_tmpl_checkout', $data);
        }

// 検証をパスした入力データは、モデルを使って保存します。
        $this->customerInfo->setData($this->request->getPost([
            'name',
            'zip',
            'addr',
            'tel',
            'email',
        ]));
        $this->customerModel->set($this->customerInfo);

        $cart = $this->cartModel->getAll();

        $data = [
            'name' => $this->customerInfo['name'],
            'zip' => $this->customerInfo['zip'],
            'addr' => $this->customerInfo['addr'],
            'tel' => $this->customerInfo['tel'],
            'email' => $this->customerInfo['email'],
            'total' => $cart['total'],
            'cart' => $cart['items'],
            'action' => '注文内容の確認',
            'main' => 'shop_confirm',
        ];

        return $this->twig->render('shop_tmpl_checkout', $data);
    }
}
