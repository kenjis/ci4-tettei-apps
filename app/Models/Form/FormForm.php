<?php

declare(strict_types=1);

namespace App\Models\Form;

use App\Libraries\FormData;

use function trim;

/**
 * コンタクトフォーム
 */
class FormForm extends FormData
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $email;

    /** @var string */
    protected $comment;

    /**
     * バリデーションのルール
     *
     * @var array<string, array<string, array<string, string>>>
     */
    protected $validationRules = [
        'common' => [
            'name' => [
                'label' => '名前',
                'rules' => 'required|max_length[20]',
            ],
            'email' => [
                'label' => 'メールアドレス',
                'rules' => 'required|valid_email',
            ],
            'comment' => [
                'label' => 'コメント',
                'rules' => 'required|max_length[200]',
            ],
        ],
    ];

    /**
     * @param array{name: string, email: string, comment: string} $data
     */
    public function setData(array $data): FormData
    {
        $this->name = trim($data['name']);
        $this->email = trim($data['email']);
        $this->comment = $data['comment'];

        return $this;
    }

    public function getName(): string
    {
        $this->isset('name');

        return $this->name;
    }

    public function getEmail(): string
    {
        $this->isset('email');

        return $this->email;
    }

    public function getComment(): string
    {
        $this->isset('comment');

        return $this->comment;
    }
}
