<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Library\CI_Email;
use Kenjis\CI3Compatible\Library\CI_Parser;
use Kenjis\CI3Compatible\Library\CI_Session;
use Kenjis\CI3Compatible\Test\TestCase\UnitTestCase;
use Kenjis\CI3Compatible\Test\Traits\SessionTest;

class ShopModelTest extends UnitTestCase
{
    use SessionTest;

    /** @var string */
    private $admin = 'admin@example.jp';

    /** @var ShopModel */
    private $shopModel;

    /** @var CI_Email */
    private $ciEmail;

    /** @var CartRepository */
    private $cartRepository;

    /** @var ProductRepository */
    private $productRepository;

    /** @var CustomerInfoRepository */
    private $customerInfoRepository;

    // region Fixture
    public function setUp(): void
    {
        parent::setUp();

        $CI =& get_instance();
        $CI->load->database();
        $session = new CI_Session();
        $this->ciEmail = new CI_Email();
        $mailModel = new MailService($this->ciEmail);
        $this->customerInfoRepository = new CustomerInfoRepository($session);
        $this->cartRepository = new CartRepository($session);
        $this->productRepository = new ProductRepository($CI->db);
        $this->shopModel = new ShopModel(
            new CI_Parser(),
            $this->customerInfoRepository,
            $mailModel,
            $this->cartRepository
        );
    }
    // endregion

    // region Tests
    public function test_order(): void
    {
        $addToCartUseCase = new AddToCartUseCase($this->cartRepository, $this->productRepository);
        $addToCartUseCase->add(1, 1);
        $addToCartUseCase->add(2, 2);

        $customerInfo = new CustomerInfoForm();
        $customerInfo->getValidationRules();
        $customerInfo->setData(
            [
                'name'  => '名前',
                'zip'   => '111-1111',
                'addr'  => '東京都千代田区',
                'tel'   => '03-3333-3333',
                'email' => 'foo@example.jp',
            ]
        );
        $this->customerInfoRepository->save($customerInfo);

        $actual = $this->shopModel->order($this->admin);

        $this->assertTrue($actual);

        $ci4MockEmail = $this->ciEmail->getCI4Library();
        $mail = $ci4MockEmail->archive;

        $this->assertEquals($this->admin, $mail['fromEmail']);
        $this->assertStringContainsString('注文合計： 11,400円', $mail['body']);
    }

    public function test_order_mail_fails(): void
    {
        $ci4MockEmail = $this->ciEmail->getCI4Library();
        $ci4MockEmail->returnValue = false;
        $mailModel = new MailService($this->ciEmail);
        $session = new CI_Session();
        $this->customerInfoRepository = new CustomerInfoRepository($session);
        $this->shopModel = new ShopModel(
            new CI_Parser(),
            $this->customerInfoRepository,
            $mailModel,
            $this->cartRepository
        );

        $addToCartUseCase = new AddToCartUseCase($this->cartRepository, $this->productRepository);
        $addToCartUseCase->add(1, 1);
        $addToCartUseCase->add(2, 2);

        $actual = $this->shopModel->order($this->admin);

        $this->assertFalse($actual);
    }
    // endregion
}
