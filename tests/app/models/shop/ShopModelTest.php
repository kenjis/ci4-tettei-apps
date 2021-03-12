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

    /** @var CartModel */
    private $cartModel;

    /** @var CI_Email */
    private $ciEmail;

    // region Fixture
    public function setUp(): void
    {
        parent::setUp();

        $CI =& get_instance();
        $CI->load->database();
        $session = new CI_Session();
        $this->cartModel = new CartModel(new InventoryModel($CI->db), $session);
        $this->ciEmail = new CI_Email();
        $mailModel = new MailModel($this->ciEmail);
        $customerModel = new CustomerModel($session);
        $this->shopModel = new ShopModel(
            $this->cartModel,
            $customerModel,
            $mailModel,
            new CI_Parser()
        );
    }
    // endregion

    // region Tests
    public function test_order(): void
    {
        $this->cartModel->add(1, 1);
        $this->cartModel->add(2, 2);

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
        $mailModel = new MailModel($this->ciEmail);
        $session = new CI_Session();
        $customerModel = new CustomerModel($session);
        $this->shopModel = new ShopModel(
            $this->cartModel,
            $customerModel,
            $mailModel,
            new CI_Parser()
        );

        $this->cartModel->add(1, 1);
        $this->cartModel->add(2, 2);

        $actual = $this->shopModel->order($this->admin);

        $this->assertFalse($actual);
    }
    // endregion
}
