<?php

declare(strict_types=1);

namespace App\Libraries;

use App\Exception\LogicException;
use ArrayAccess;
use Iterator;

use function array_key_exists;
use function array_keys;
use function array_merge;
use function assert;
use function in_array;
use function is_string;
use function property_exists;

/**
 * HTMLフォームのデータ
 *
 * @implements ArrayAccess<string, string>
 * @implements Iterator<string, string>
 */
abstract class FormData implements ArrayAccess, Iterator
{
    /**
     * 配列としてアクセスできるプロパティのリスト
     *
     * @var string[]
     */
    protected $arrayReadProperties = [];

    /** @var int イテレータの位置 */
    protected $position = 0;

    /**
     * バリデーションのルール
     *
     * @var array<string, array<string, array<string, string>>>
     */
    protected $validationRules = [];

    /**
     * 現在のバリデーションルール
     *
     * バリデーションルールはグループ名をキーにグループ化する。
     * 共通のルールは common グループに設定する。
     *
     * @var array<string, array<string, string>>
     */
    protected $currentRules;

    /**
     * @param array<string, mixed> $data
     */
    abstract public function setData(array $data): FormData;

    /**
     * @return array<string, array<string, string>>
     */
    public function getValidationRules(string $group = 'common'): array
    {
        assert(array_key_exists('common', $this->validationRules));

        $rules = $this->validationRules['common'];

        if (array_key_exists($group, $this->validationRules)) {
            $rules = array_merge($rules, $this->validationRules[$group]);
        }

        $this->currentRules = $rules;

        $this->arrayReadProperties = array_keys($rules);

        return $this->currentRules;
    }

    /**
     * @return array<string, mixed>
     */
    public function asArray(): array
    {
        assert(
            isset($this->arrayReadProperties),
            'プロパティ $arrayReadProperties に配列としてアクセスできるプロパティを設定してください。'
        );

        $array = [];

        foreach ($this->arrayReadProperties as $property) {
            $array[$property] = $this[$property];
        }

        return $array;
    }

    protected function isset(string $property): void
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

    public function current()
    {
        $property = $this->arrayReadProperties[$this->position];

        return $this->$property;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function key()
    {
        return $this->arrayReadProperties[$this->position];
    }

    public function valid()
    {
        return isset($this->arrayReadProperties[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }
}
