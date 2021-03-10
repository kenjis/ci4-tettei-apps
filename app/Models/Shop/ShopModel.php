<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Core\CI_Loader;
use Kenjis\CI3Compatible\Core\CI_Model;
use Kenjis\CI3Compatible\Library\CI_Parser;

use function array_merge;

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
// 買い物かごの情報を取得します。
        $data = $this->cartModel->getCart()->getOrderConfirmationData();

// お客様情報を取得します。
        $data = array_merge($data, $this->customerModel->get());

        // @phpstan-ignore-next-line
        $mail = $this->createMailData($data, $adminEmail);

// sendmail()メソッドを呼び出し、実際にメールを送信します。メール送信に成功
// すれば、TRUEを返します。
        if ($this->mailModel->sendmail($mail)) {
            return true;
        }

        return false;
    }

    /**
     * @param array{date: string, items: array<int, array{id: int, qty: int, name: string, price: string, amount: string}>, line: int, total: string, name: string, zip: string, addr: string, tel: string, email: string} $data
     *
     * @return array{from_name: string, from: string, to: string, bcc: string, subject: string, body: string}
     */
    private function createMailData(array $data, string $adminEmail): array
    {
        // テンプレートパーサクラスでメール本文を作成します。
        $this->load->library('parser');

        $body = $this->parser->parse(
            'templates/mail/shop_order',
            $data,
            true
        );

// メールのヘッダを設定します。Bccで同じメールを管理者にも送るようにします。
        return [
            'from_name' => 'CIショップ',
            'from'      => $adminEmail,
            'to'        => $data['email'],
            'bcc'       => $adminEmail,
            'subject'   => '【注文メール】CIショップ',
            'body'      => $body,
        ];
    }
}
