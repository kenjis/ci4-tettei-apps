<?php

declare(strict_types=1);

namespace App\Models\Form;

use Kenjis\CI3Compatible\Exception\LogicException;

use function trim;

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
     * @param array<string, string> $data
     */
    public function setData(array $data): void
    {
        $this->name = trim($data['name']);
        $this->email = trim($data['email']);
        $this->comment = $data['comment'];
    }

    /**
     * @return array<string, string>
     */
    public function asArray(): array
    {
        return [
            'name' => $this->get('name'),
            'email' => $this->get('email'),
            'comment' => $this->get('comment'),
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function getValidationRules(): array
    {
        return $this->validationRules;
    }

    private function get(string $property): string
    {
        if ($this->$property === null) {
            throw new LogicException(
                'setData() でデータをセットしてください。'
            );
        }

        return $this->$property;
    }

    public function getName(): string
    {
        return $this->get('name');
    }

    public function getEmail(): string
    {
        return $this->get('email');
    }

    public function getComment(): string
    {
        return $this->get('comment');
    }
}
