<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Core\CI_Loader;
use Kenjis\CI3Compatible\Core\CI_Model;
use Kenjis\CI3Compatible\Library\CI_Parser;

use function array_merge;
use function date;
use function number_format;

/**
 * @property CartModel $cartModel
 * @property CustomerModel $customerModel
 * @property MailModel $mailModel
 * @property CI_Parser $parser
 * @property CI_Loader $load
 */
class ShopModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('shop/cartModel');
        $this->load->model('shop/customerModel');
        $this->load->model('shop/mailModel');
    }

    /**
     * 注文の処理
     */
    public function order(string $adminEmail): bool
    {
// 注文日時をPHPのdate()関数から取得します。
        $date = date('Y/m/d H:i:s');

// カートの情報を取得します。
        $cart = $this->cartModel->getAll();
        foreach ($cart['items'] as &$item) {
            $item['price']  = number_format((float) $item['price']);
            $item['amount'] = number_format((float) $item['amount']);
        }

        $data = [
            'date'  => $date,
            'items' => $cart['items'],
            'line'  => $cart['line'],
            'total' => number_format($cart['total']),
        ];

// お客様情報を取得します。
        $data = array_merge($data, $this->customerModel->get());

// テンプレートパーサクラスでメール本文を作成します。
        $this->load->library('parser');
        $body = $this->parser->parse(
            'templates/mail/shop_order',
            $data,
            true
        );

// メールのヘッダを設定します。Bccで同じメールを管理者にも送るようにします。
        $mail = [
            'from_name' => 'CIショップ',
            'from'      => $adminEmail,
            'to'        => $data['email'],
            'bcc'       => $adminEmail,
            'subject'   => '【注文メール】CIショップ',
            'body'      => $body,
        ];

// sendmail()メソッドを呼び出し、実際にメールを送信します。メール送信に成功
// すれば、TRUEを返します。
        if ($this->mailModel->sendmail($mail)) { // @phpstan-ignore-line 何故か 'to' が array|int|string と判定されてエラーになる
            return true;
        }

        return false;
    }
}
