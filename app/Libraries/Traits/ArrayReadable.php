<?php

declare(strict_types=1);

namespace App\Libraries\Traits;

use LogicException;

use function assert;
use function in_array;
use function is_string;
use function property_exists;

/**
 *  implements ArrayAccess
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
            isset($this->arrayReadProperties),
            'プロパティ $arrayReadProperties に配列としてアクセスできるプロパティを設定してください。'
        );

        if (! property_exists($this, $offset)) {
            throw new LogicException(
                $offset . ' は存在しません。'
            );
        }

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
}
