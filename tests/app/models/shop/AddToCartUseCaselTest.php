<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Library\CI_Session;
use Kenjis\CI3Compatible\Test\TestCase\UnitTestCase;
use Kenjis\CI3Compatible\Test\Traits\SessionTest;

use function get_instance;

class AddToCartUseCaselTest extends UnitTestCase
{
    use SessionTest;

    /** @var AddToCartUseCase */
    private $addToCartUseCase;

    /** @var CartRepository */
    private $cartRepository;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $CI =& get_instance();
        $CI->load->library('seeder');
        $CI->seeder->call('ProductSeeder');
    }

    public function setUp(): void
    {
        parent::setUp();

        // CartRepository が Session に依存しているためリセットする
        $_SESSION = [];

        $CI =& get_instance();
        $CI->load->database();

        $this->cartRepository = new CartRepository(new CI_Session());
        $this->addToCartUseCase = new AddToCartUseCase(
            $this->cartRepository,
            new ProductRepository($CI->db)
        );
    }

    public function test_add(): void
    {
        $this->addToCartUseCase->add(1, 1);
        $this->addToCartUseCase->add(2, 2);

        $cart = $this->cartRepository->find();
        $actual = $cart->getLineCount();

        $this->assertEquals(2, $actual);
    }

    public function test_delete(): void
    {
        $this->addToCartUseCase->add(2, 2);
        $this->addToCartUseCase->add(2, 0);

        $cart = $this->cartRepository->find();
        $actual = $cart->getLineCount();

        $this->assertEquals(0, $actual);
    }
}
