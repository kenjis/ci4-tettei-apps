<?php

declare(strict_types=1);

namespace App\Models\Form;

use Kenjis\CI3Compatible\Exception\LogicException;

use function trim;

/**
 * コンタクトフォーム
 */
class FormForm
{
    /** @var string */
    private $name;

    /** @var string */
    private $email;

    /** @var string */
    private $comment;

    /**
     * バリデーションのルール
     *
     * @var array<string, array<string, string>>
     */
    private $validationRules = [
        'name' => [
            'label' => '名前',
            'rules' => 'trim|required|max_length[20]',
        ],
        'email' => [
            'label' => 'メールアドレス',
            'rules' => 'trim|required|valid_email',
        ],
        'comment' => [
            'label' => 'コメント',
            'rules' => 'required|max_length[200]',
        ],
    ];

    /**
     * @param array{name: string, email: string, comment: string} $data
     */
    public function setData(array $data): void
    {
        $this->name = trim($data['name']);
        $this->email = trim($data['email']);
        $this->comment = $data['comment'];
    }

    /**
     * @return array{name: string, email: string, comment: string}
     */
    public function asArray(): array
    {
        return [
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'comment' => $this->getComment(),
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function getValidationRules(): array
    {
        return $this->validationRules;
    }

    private function isset(string $property): void
    {
        if ($this->$property === null) {
            throw new LogicException(
                'setData() でデータをセットしてください。'
            );
        }
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
