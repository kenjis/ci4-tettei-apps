<?php

declare(strict_types=1);

namespace App\Models\Shop;

use ArrayAccess;
use Kenjis\CI3Compatible\Exception\LogicException;

use function assert;
use function in_array;
use function is_string;
use function property_exists;
use function trim;

/**
 * お客様情報
 *
 * @implements ArrayAccess<string, string>
 */
class CustomerInfoForm implements ArrayAccess
{
    /** @var string */
    private $name;

    /** @var string */
    private $zip;

    /** @var string */
    private $addr;

    /** @var string */
    private $tel;

    /** @var string */
    private $email;

    /** @var string[] */
    private $arrayReadProperties = [
        'name',
        'zip',
        'addr',
        'tel',
        'email',
    ];

    /**
     * バリデーションのルール
     *
     * @var array<string, array<string, string>>
     */
    private $validationRules = [
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
