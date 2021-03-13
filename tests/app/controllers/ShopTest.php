<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Shop\CustomerInfo;
use App\Controllers\Shop\Order;
use App\Models\Shop\Cart;
use App\Models\Shop\CartItem;
use App\Models\Shop\CartRepository;
use App\Models\Shop\CustomerInfoRepository;
use App\Models\Shop\ShopModel;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\Session\Session;
use CodeIgniter\Validation\Validation;
use Config\Services;
use Kenjis\CI3Compatible\Library\CI_Form_validation;
use Kenjis\CI3Compatible\Test\TestCase\FeatureTestCase;
use Kenjis\CI3Compatible\Test\Traits\SessionTest;
use Kenjis\CI3Compatible\Test\Traits\UnitTest;
use Twig\Environment;

use function get_instance;

class ShopTest extends FeatureTestCase
{
    use UnitTest;
    use SessionTest;

    // region Fixture
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $CI =& get_instance();
        $CI->load->library('seeder');
        $CI->seeder->call('ProductSeeder');
    }
    // endregion

    // region Feature Tests
    public function test_index(): void
    {
        $output = $this->request('GET', 'shop/index/1');

        $this->assertStringContainsString('<title>CIショップ</title>', $output);
    }

    public function test_product(): void
    {
        $output = $this->request('GET', 'shop/product/1');

        $this->assertStringContainsString('CodeIgniter徹底入門', $output);
    }

    public function test_add(): void
    {
        $output = $this->request('POST', 'shop/add/3', ['qty' => '3']);

        $this->assertStringContainsString('CodeIgniter徹底入門 DVD', $output);
    }

    public function test_search(): void
    {
        $output = $this->request('GET', 'shop/search', ['q' => '徹底入門']);

        $this->assertStringContainsString('「徹底入門」の検索結果', $output);
    }

    public function test_customer_info(): void
    {
        $output = $this->request('POST', 'shop/add/1', ['qty' => '1']);

        $this->assertStringContainsString('CodeIgniter徹底入門', $output);

        $output = $this->request('GET', 'shop/cart');

        $this->assertStringContainsString('買い物かご', $output);
        $this->assertStringContainsString('CodeIgniter徹底入門', $output);

        $output = $this->request('POST', 'shop/customer_info');

        $this->assertStringContainsString('お客様情報の入力', $output);
    }
    // endregion

    // region Unit Tests
    public function test_confirm_pass(): void
    {
        $this->resetServices();
        $request = $this->getDouble(
            IncomingRequest::class,
            [
                'getMethod' => 'post',
                'getLocale' => 'ja',
                'getPost' => [
                    'name'  => '名前',
                    'zip'   => '111-1111',
                    'addr'  => '東京都千代田区',
                    'tel'   => '03-3333-3333',
                    'email' => 'foo@example.jp',
                ],
            ]
        );
        $session = $this->getDouble(
            Session::class,
            []
        );
        Services::injectMock('request', $request);
        Services::injectMock('session', $session);

        /**
         * @var CustomerInfo
         */
        $customerInfoController = $this->newController(CustomerInfo::class);

        $customerInfoRepository = $this->getDouble(CustomerInfoRepository::class, []);
        $this->verifyInvokedOnce($customerInfoRepository, 'save');

        $validation = $this->getDouble(
            Validation::class,
            ['run' => true],
            false
        );
        Services::injectMock('validation', $validation);

        $twig = $this->getDouble(Environment::class, []);
        $this->verifyInvokedMultipleTimes(
            $twig,
            'render',
            1,
            [
                ['shop_tmpl_checkout', $this->anything()],
            ]
        );

        $this->setPrivateProperty($customerInfoController, 'twig', $twig);
        $this->setPrivateProperty(
            $customerInfoController,
            'customerInfoRepository',
            $customerInfoRepository
        );

        $customerInfoController->confirm();
    }

    public function test_confirm_fail(): void
    {
        /**
         * @var CustomerInfo
         */
        $customerInfoController = $this->newController(CustomerInfo::class);

        $customerInfoRepository = $this->getDouble(CustomerInfoRepository::class, []);
        $this->verifyNeverInvoked($customerInfoRepository, 'save');
        $validation = $this->getDouble(
            CI_Form_validation::class,
            ['run' => false],
            true
        );
        $twig = $this->getDouble(Environment::class, []);

        $data['action'] = 'お客様情報の入力';
        $data['main']   = 'shop_customer_info';
        $this->verifyInvokedMultipleTimes(
            $twig,
            'render',
            1,
            [
                ['shop_tmpl_checkout', $data],
            ]
        );
        $customerInfoController->form_validation = $validation;
        $this->setPrivateProperty($customerInfoController, 'twig', $twig);
        $this->setPrivateProperty(
            $customerInfoController,
            'customerInfoRepository',
            $customerInfoRepository
        );

        $customerInfoController->confirm();
    }

    public function test_order_cart_is_empty(): void
    {
        $_SESSION = [];

        /**
         * @var Order
         */
        $obj = $this->newController(Order::class);

        $cart = new Cart();
        $cartRepository = $this->getDouble(
            CartRepository::class,
            ['find' => $cart]
        );
        $this->setPrivateProperty($obj, 'cartRepository', $cartRepository);

        $output = $obj->index();

        $this->assertStringContainsString('買い物カゴには何も入っていません', $output);
    }

    public function test_order(): void
    {
        $_SESSION = [];

        /**
         * @var Order
         */
        $obj = $this->newController(Order::class);

        $cart = new Cart();
        $cartItem = new CartItem(1, 1, 'name', 100);
        $cart->add($cartItem);
        $cartRepository = $this->getDouble(
            CartRepository::class,
            ['find' => $cart]
        );
        $this->setPrivateProperty($obj, 'cartRepository', $cartRepository);

        $shop = $this->getDouble(ShopModel::class, ['order' => true]);
        $this->setPrivateProperty($obj, 'shopModel', $shop);

        $output = $obj->index();

        $this->assertStringContainsString('ご注文ありがとうございます', $output);
    }

    public function test_order_system_error(): void
    {
        $_SESSION = [];

        /**
         * @var Order
         */
        $obj = $this->newController(Order::class);

        $cart = new Cart();
        $cartItem = new CartItem(1, 1, 'name', 100);
        $cart->add($cartItem);
        $cartRepository = $this->getDouble(
            CartRepository::class,
            ['find' => $cart]
        );
        $this->setPrivateProperty($obj, 'cartRepository', $cartRepository);

        $shop = $this->getDouble(ShopModel::class, ['order' => false]);
        $this->setPrivateProperty($obj, 'shopModel', $shop);

        $output = $obj->index();

        $this->assertStringContainsString('システムエラー', $output);
    }
    // endregion
}
