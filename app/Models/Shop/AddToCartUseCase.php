<?php

declare(strict_types=1);

namespace App\Models\Shop;

class AddToCartUseCase
{
    /** @var InventoryModel */
    private $inventoryModel;

    /** @var CartRepository */
    private $cartRepository;

    public function __construct(CartRepository $cartRepository, InventoryModel $inventoryModel)
    {
        $this->cartRepository = $cartRepository;
        $this->inventoryModel = $inventoryModel;
    }

    /**
     * 買い物かごに追加/削除
     *
     * @param int $id  商品ID
     * @param int $qty 数量
     */
    public function add(int $id, int $qty): void
    {
        $cart = $this->cartRepository->find();

// 商品IDと数量を引数として渡され、数量が0以下の場合は、買い物かごからその商品を
// 削除します。
        if ($qty <= 0) {
            $cart->remove($id);
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
            $cart->add($item);
        }

// 買い物かごを保存します。
        $this->cartRepository->save($cart);
    }
}
