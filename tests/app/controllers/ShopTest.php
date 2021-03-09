<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Shop\CartModel;
use App\Models\Shop\CustomerModel;
use App\Models\Shop\ShopModel;
use Kenjis\CI3Compatible\Library\CI_Form_validation;
use Kenjis\CI3Compatible\Test\TestCase\FeatureTestCase;
use Kenjis\CI3Compatible\Test\Traits\UnitTest;
use Twig\Environment;

use function get_instance;

class ShopTest extends FeatureTestCase
{
    use UnitTest;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $CI =& get_instance();
        $CI->load->library('seeder');
        $CI->seeder->call('ProductSeeder');
    }

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

    public function test_confirm_pass(): void
    {
        /**
         * @var Shop
         */
        $obj = $this->newController(Shop::class);

        $model = $this->getDouble(CustomerModel::class, []);
        $this->verifyInvokedOnce($model, 'set');

        $validation = $this->getDouble(
            CI_Form_validation::class,
            ['run' => true],
            true
        );

        $twig = $this->getDouble(Environment::class, []);
        $this->verifyInvokedMultipleTimes(
            $twig,
            'render',
            1,
            [
                ['shop_tmpl_checkout', $this->anything()],
            ]
        );

        $obj->form_validation = $validation;
        $this->setPrivateProperty($obj, 'twig', $twig);
        $obj->customerModel = $model;

        $obj->confirm();
    }

    public function test_confirm_fail(): void
    {
        /**
         * @var Shop
         */
        $obj = $this->newController(Shop::class);

        $model = $this->getDouble(CustomerModel::class, []);
        $this->verifyNeverInvoked($model, 'set');
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
        $obj->form_validation = $validation;
        $this->setPrivateProperty($obj, 'twig', $twig);
        $obj->Customer_model = $model;

        $obj->confirm();
    }

    public function test_order_cart_is_empty(): void
    {
        /**
         * @var Shop
         */
        $obj = $this->newController(Shop::class);

        $cart = $this->getDouble(CartModel::class, ['count' => 0]);
        $obj->cartModel = $cart;

        $output = $obj->order();

        $this->assertStringContainsString('買い物カゴには何も入っていません', $output);
    }

    public function test_order(): void
    {
        /**
         * @var Shop
         */
        $obj = $this->newController(Shop::class);

        $cart = $this->getDouble(CartModel::class, ['count' => 1]);
        $shop = $this->getDouble(ShopModel::class, ['order' => true]);
        $obj->cartModel = $cart;
        $obj->shopModel = $shop;

        $output = $obj->order();

        $this->assertStringContainsString('ご注文ありがとうございます', $output);
    }

    public function test_order_system_error(): void
    {
        /**
         * @var Shop
         */
        $obj = $this->newController(Shop::class);

        $cart = $this->getDouble(CartModel::class, ['count' => 1]);
        $shop = $this->getDouble(ShopModel::class, ['order' => false]);
        $obj->cartModel = $cart;
        $obj->shopModel = $shop;

        $output = $obj->order();

        $this->assertStringContainsString('システムエラー', $output);
    }
}
