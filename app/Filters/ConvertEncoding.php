<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

use function assert;
use function count;
use function is_string;
use function mb_convert_encoding;

class ConvertEncoding implements FilterInterface
{
    /**
     * @param mixed $arguments
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function before(RequestInterface $request, $arguments = null): void
    {
// 携帯端末からのアクセスを判定するためユーザエージェントクラスをロードします。
        $agent = $request->getUserAgent();

// 携帯端末からの入力文字エンコードを変換します。
        if (count($_POST) < 1 || ! $agent->isMobile()) {
            return;
        }

        $_POST = $this->convertToUtf8($_POST);

// Requestオブジェクトに変換した$_POSTを設定します。
        $request->setGlobal('post', $_POST);
    }

    /**
     * @param mixed $arguments
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function after(
        RequestInterface $request,
        ResponseInterface $response,
        $arguments = null
    ): void {
        $agent = $request->getUserAgent();

// 携帯端末の場合は、HTTPヘッダのContent-Typeヘッダで文字エンコードがShift_JIS
// である旨を出力し、送信するコンテンツもShift_JISに変換したものを送ります。
        if (! $agent->isMobile()) {
            return;
        }

        $response->setHeader(
            'Content-Type',
            'text/html; charset=Shift_JIS'
        );

        $body = $response->getBody();
        assert(is_string($body));

        $response->setBody(
            mb_convert_encoding($body, 'SJIS-win', 'UTF-8')
        );
    }

    /**
     * 入力文字エンコード変換
     *
     * @param array<string, mixed> $array
     *
     * @return array<string, mixed>
     */
    private function convertToUtf8(array $array): array
    {
        // @phpstan-ignore-next-line
        $utf8 = mb_convert_encoding($array, 'UTF-8', 'SJIS-win');

        if ($utf8 === false) { // @phpstan-ignore-line
            return [];
        }

        return $utf8; // @phpstan-ignore-line
    }
}
