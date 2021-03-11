<?php

declare(strict_types=1);

namespace App\Models\Shop;

use App\Libraries\FormData;

use function trim;

/**
 * お客様情報
 */
class CustomerInfoForm extends FormData
{
    /** @var string */
    protected $name; // phpcs:ignore

    /** @var string */
    protected $zip; // phpcs:ignore

    /** @var string */
    protected $addr; // phpcs:ignore

    /** @var string */
    protected $tel; // phpcs:ignore

    /** @var string */
    protected $email; // phpcs:ignore

    /** @var string[] */
    protected $arrayReadProperties = [
        'name',
        'zip',
        'addr',
        'tel',
        'email',
    ];

    /** @var int イテレータの位置 */
    protected $position = 0;

    /**
     * バリデーションのルール
     *
     * @var array<string, array<string, string>>
     */
    protected $validationRules = [
        'name' => [
            'label' => '名前',
            'rules' => 'trim|required|max_length[64]',
        ],
        'zip' => [
            'label' => '郵便番号',
            'rules' => 'trim|max_length[8]',
        ],
        'addr' => [
            'label' => '住所',
            'rules' => 'trim|required|max_length[128]',
        ],
        'tel' => [
            'label' => '電話番号',
            'rules' => 'trim|required|max_length[20]',
        ],
        'email' => [
            'label' => 'メールアドレス',
            'rules' => 'trim|required|valid_email|max_length[64]',
        ],
    ];

    /**
     * @param array{name: string, zip: string, addr: string, tel: string, email: string} $data
     */
    public function setData(array $data): void
    {
        $this->name = trim($data['name']);
        $this->zip = trim($data['zip']);
        $this->addr = trim($data['addr']);
        $this->tel = trim($data['tel']);
        $this->email = trim($data['email']);
    }
}
