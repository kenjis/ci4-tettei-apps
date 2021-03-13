<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Library\CI_Session;

class CartRepository
{
    /** @var CI_Session */
    private $session;

    public function __construct(CI_Session $session)
    {
        $this->session = $session;
    }

    public function find(): Cart
    {
        $cart = $this->session->userdata('Cart');

        if ($cart !== null) {
            return $cart;
        }

        return new Cart();
    }

    public function save(Cart $cart): void
    {
// セッションに買い物かごを保存します。
        $this->session->set_userdata('Cart', $cart);
    }
}
