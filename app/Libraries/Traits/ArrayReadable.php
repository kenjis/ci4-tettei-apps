<?php

declare(strict_types=1);

namespace App\Libraries\Traits;

use App\Exception\LogicException;

use function assert;
use function in_array;
use function is_string;
use function property_exists;

/**
 * 配列としてリード可能にする
 *
 * $arrayReadProperties に記載したプロパティは $obj['key'] としてリード可能になる。
 * また、$obj->key でもリード可能。
 *
 * 使用するクラスで implements ArrayAccess を記載すること。
 */
trait ArrayReadable
{
    /**
     * @param string|int $offset
     */
    public function offsetExists($offset): bool
    {
        assert(is_string($offset));
        assert(
            property_exists($this, 'arrayReadProperties')
            && ! empty($this->arrayReadProperties),
            'プロパティ $arrayReadProperties に配列としてアクセスできるプロパティを設定してください。'
        );

        if (! property_exists($this, $offset)) {
            return false;
        }

        return in_array($offset, $this->arrayReadProperties, true);
    }

    /**
     * @param string|int $offset
     */
    public function offsetGet($offset): mixed
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

    /**
     * @return array<string, mixed>
     */
    public function asArray(): array
    {
        assert(
            property_exists($this, 'arrayReadProperties')
            && ! empty($this->arrayReadProperties),
            'プロパティ $arrayReadProperties に配列としてアクセスできるプロパティを設定してください。'
        );

        $array = [];

        foreach ($this->arrayReadProperties as $property) {
            $array[$property] = $this[$property];
        }

        return $array;
    }

    /**
     * @return mixed
     */
    public function __get(string $key)
    {
        if ($this->offsetExists($key)) {
            return $this->$key;
        }

        throw new LogicException(
            $key . ' はアクセスできません。'
        );
    }
}
