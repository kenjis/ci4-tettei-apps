<?php

declare(strict_types=1);

namespace App\Models\Bbs;

use App\Libraries\FormData;

use function array_key_exists;
use function trim;

/**
 * 投稿
 */
class PostForm extends FormData
{
    /** @var string */
    protected $name; // phpcs:ignore

    /** @var string */
    protected $email; // phpcs:ignore

    /** @var string */
    protected $subject; // phpcs:ignore

    /** @var string */
    protected $body; // phpcs:ignore

    /** @var string */
    protected $password; // phpcs:ignore

    /** @var string */
    protected $captcha; // phpcs:ignore

    /** @var int */
    protected $key; // phpcs:ignore

    /**
     * バリデーションのルール
     *
     * @var array<string, array<string, array<string, string>>>
     */
    protected $validationRules = [
        'common' => [
            'name' => [
                'label' => '名前',
                'rules' => 'trim|required|max_length[16]',
            ],
            'email' => [
                'label' => 'メールアドレス',
                'rules' => 'trim|permit_empty|valid_email|max_length[64]',
            ],
            'subject' => [
                'label' => '件名',
                'rules' => 'trim|required|max_length[32]',
            ],
            'body' => [
                'label' => '内容',
                'rules' => 'trim|required|max_length[200]',
            ],
            'password' => [
                'label' => '削除パスワード',
                'rules' => 'max_length[32]',
            ],
        ],
        'confirm' => [
            'captcha' => [
                'label' => '画像認証コード',
                'rules' => 'trim|required|alpha_numeric|captcha_check[{key}]',
            ],
// keyフィールドは、キャプチャのID番号です。隠しフィールドに仕込まれるのみで
// ユーザの目に触れることはありません。
            'key' => [
                'label' => 'key',
                'rules' => 'numeric',
            ],
        ],
    ];

    /**
     * @param array{name: string, email: string, subject: string, body: string, password: string, captcha: string, key: string} $data
     */
    public function setData(array $data): FormData
    {
        $this->name = trim($data['name']);
        $this->email = trim($data['email']);
        $this->subject = trim($data['subject']);
        $this->body = trim($data['body']);
        $this->password = trim($data['password']);

        if (array_key_exists('captcha', $this->currentRules)) {
            $this->captcha = trim($data['captcha']);
        }

        if (array_key_exists('key', $this->currentRules)) {
            $this->key = (int) $data['key'];
        }

        return $this;
    }
}
