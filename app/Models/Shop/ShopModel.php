<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Library\CI_Parser;

use function array_merge;

class ShopModel
{
    /** @var CustomerInfoRepository */
    private $customerInfoRepository;

    /** @var MailModel */
    private $mailModel;

    /** @var CI_Parser */
    private $parser;

    /** @var CartRepository */
    private $cartRepository;

    public function __construct(
        CustomerInfoRepository $customerInfoRepository,
        MailModel $mailModel,
        CI_Parser $parser,
        CartRepository $cartRepository
    ) {
        $this->customerInfoRepository = $customerInfoRepository;
        $this->mailModel = $mailModel;
        $this->parser = $parser;
        $this->cartRepository = $cartRepository;
    }

    /**
     * 注文の処理
     */
    public function order(string $adminEmail): bool
    {
// 買い物かごの情報を取得します。
        $cart = $this->cartRepository->find();
        $data = $cart->getOrderConfirmationData();

// お客様情報を取得します。
        $customerInfo = $this->customerInfoRepository->find();
        $data = array_merge($data, $customerInfo->asArray());

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
        $body = $this->parser->parse(
            'templates/mail/shop_order',
            $data,
            true
        );

// メールのデータを作成します。Bccで同じメールを管理者にも送るようにします。
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
