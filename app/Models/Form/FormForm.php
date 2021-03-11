<?php

declare(strict_types=1);

namespace App\Models\Form;

use ArrayAccess;
use Kenjis\CI3Compatible\Exception\LogicException;

use function assert;
use function in_array;
use function is_string;
use function property_exists;
use function trim;

/**
 * コンタクトフォーム
 *
 * @implements ArrayAccess<string, string>
 */
class FormForm implements ArrayAccess
{
    /** @var string */
    private $name;

    /** @var string */
    private $email;

    /** @var string */
    private $comment;

    /** @var string[] */
    private $arrayReadProperties = [
        'name',
        'email',
        'comment',
    ];

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

    /**
     * @param string|int $offset
     */
    public function offsetExists($offset): bool
    {
        assert(is_string($offset));

        if (! isset($this->arrayReadProperties)) {
            throw new LogicException(
                'プロパティ $arrayReadProperties に配列としてアクセスできるプロパティを設定してください。'
            );
        }

        if (! property_exists($this, $offset)) {
            throw new LogicException(
                $offset . ' は存在しません。'
            );
        }

        $this->isset($offset);

        return in_array($offset, $this->arrayReadProperties, true);
    }

    /**
     * @param string|int $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->$offset;
        }

        throw new LogicException($offset . ' は存在しません。');
    }

    /**
     * @param string|int $offset
     * @param mixed      $value
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function offsetSet($offset, $value): void
    {
        throw new LogicException($offset . 'は変更できません。');
    }

    /**
     * @param string|int $offset
     */
    public function offsetUnset($offset): void
    {
        throw new LogicException($offset . 'は変更できません。');
    }
}
