<?php

declare(strict_types=1);

namespace App\Libraries;

use DateTimeImmutable;

/**
 * システム全体の時間を管理するクラス
 */
class SystemClock
{
    /**
     * テスト用の固定した日時
     *
     * @var ?DateTimeImmutable
     */
    private $fixedDateTime;

    /**
     * 現在時刻を返す
     */
    public function now(): DateTimeImmutable
    {
        if ($this->fixedDateTime) {
            return $this->fixedDateTime;
        }

        return new DateTimeImmutable();
    }

    /**
     * 現在時刻を固定する（テスト用）
     */
    public function freeze(string $dateString): void
    {
        $this->fixedDateTime = new DateTimeImmutable($dateString);
    }
}
