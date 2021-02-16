<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

use function array_map;
use function count;
use function is_array;
use function mb_convert_encoding;

class ConvertEncoding implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null): void
    {
// 携帯端末からのアクセスを判定するためユーザエージェントクラスをロードします。
        $agent = $request->getUserAgent();

// 携帯端末からの入力文字エンコードを変換します。
        if (count($_POST) < 1 || ! $agent->isMobile()) {
            return;
        }

        $_POST = $this->convert_to_utf8($_POST);

// Requestオブジェクトに変換した$_POSTを設定します。
        $request->setGlobal('post', $_POST);
    }

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

        $response->setBody(
            mb_convert_encoding($response->getBody(), 'SJIS-win', 'UTF-8')
        );
    }

    // 入力文字エンコード変換
    private function convert_to_utf8($array)
    {
// 引数が配列の場合は、配列の各々の要素を自分自身に渡し処理します。
// array_map()関数の第1引数は、コールバック関数ですが、ここでは、クラス内の
// メソッドを指定しますので、[$this, 'convert_to_utf8']と配列
// で渡す必要があります。
        if (is_array($array)) {
            return array_map([$this, 'convert_to_utf8'], $array);
        }

        return mb_convert_encoding($array, 'UTF-8', 'SJIS-win');
    }
}
