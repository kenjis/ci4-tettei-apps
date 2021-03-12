<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Core\CI_Model;
use Kenjis\CI3Compatible\Library\CI_Session;

class CartModel extends CI_Model
{
    /** @var Cart */
    private $cart;

    /** @var InventoryModel */
    private $inventoryModel;

    /** @var CI_Session */
    private $session;

    public function __construct(InventoryModel $inventoryModel, CI_Session $session)
    {
        parent::__construct();

        $this->inventoryModel = $inventoryModel;
        $this->session = $session;

        $this->restoreCart();
    }

    private function restoreCart(): void
    {
        $this->cart = $this->session->userdata('Cart');
        if ($this->cart !== null) {
            return;
        }

        $this->cart = new Cart();
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    /**
     * 買い物かごに追加/削除
     *
     * @param int $id  商品ID
     * @param int $qty 数量
     */
    public function add(int $id, int $qty): void
    {
// 商品IDと数量を引数として渡され、数量が0以下の場合は、買い物かごからその商品を
// 削除します。
        if ($qty <= 0) {
            $this->cart->remove($id);
        } elseif ($this->inventoryModel->isAvailableProductItem($id)) {
// 指定の数量が1以上の場合は、その商品が存在するかチェックした後に、商品と数量を
// 買い物かごに追加します。
            $product = $this->inventoryModel->getProductItem($id);
            $item = new CartItem(
                $product->id,
                $qty,
                $product->name,
                $product->price
            );
            $this->cart->add($item);
        }

// セッションに買い物かごを保存します。
        $this->session->set_userdata('Cart', $this->cart);
    }

    /**
     * 買い物かごの情報を取得
     *
     * @return array{items: array<int, CartItem>, line: int, total: int}
     */
    public function getAll(): array
    {
        return [
            'items' => $this->cart->getItems(),     // 商品情報の配列
            'line' => $this->cart->getLineCount(),  // 商品アイテム数
            'total' => $this->cart->getTotal(),     // 合計金額
        ];
    }

    /**
     * 買い物かごに入っている商品アイテム数を返す
     */
    public function count(): int
    {
        return $this->cart->getLineCount();
    }
}
