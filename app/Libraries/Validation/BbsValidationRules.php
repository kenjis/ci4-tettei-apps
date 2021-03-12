<?php

declare(strict_types=1);

namespace App\Libraries\Validation;

use Kenjis\CI3Compatible\Database\CI_DB;

use function assert;
use function get_instance;
use function time;

class BbsValidationRules
{
    /** @var CI_DB */
    private $db;

    public function __construct()
    {
        $CI = get_instance();
        $CI->load->database();
        $this->db = $CI->db; // @phpstan-ignore-line
        assert($this->db instanceof CI_DB);
    }

    /**
     * キャプチャの検証をするメソッド
     *
     * バリデーション(認証)クラスより呼ばれる
     *
     * @param string               $str    検証する文字列
     * @param string               $fields パラメータ文字列
     * @param array<string, mixed> $data   全検証データの配列
     * @param string|null          $error  エラーメッセージ
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function captcha_check(string $str, string $fields, array $data, ?string &$error): bool
    {
// 環境がtestingまたはacceptanceの場合は、キャプチャの検証をスキップします。
        if (ENVIRONMENT === 'testing' || ENVIRONMENT === 'acceptance') { // @phpstan-ignore-line
            if ($str === '8888') {
                return true;
            }
        }

        $key = (int) $fields;

// 有効期限を2時間に設定し、それ以前に生成されたキャプチャをデータベースから
// 削除します。delete()メソッドの第2引数では、「captcha_time <」を配列のキーに
// していますが、このように記述することで、WHERE句の条件の演算子を指定できます。
        $expiration = time() - 7200;    // 有効期限 2時間
        $this->db->delete('captcha', ['captcha_time <' => $expiration]);

// バリデーション(検証)クラスより引数$strに渡された、ユーザからの入力値がデータ
// ベースに保存されている値と一致するかどうかを調べます。隠しフィールドである
// keyフィールドの値と$strを条件に、有効期限内のレコードをデータベースから
// 検索します。条件に合うレコードが存在すれば、一致したと判断します。
// where()メソッドは、複数回呼ばれると、AND条件になります。
        $this->db->select('COUNT(*) AS count');
        $this->db->where('word', $str);
        $this->db->where('captcha_id', $key);
        $this->db->where('captcha_time >', $expiration);
        $query = $this->db->get('captcha');
        $row = $query->row();

// レコードが0件の場合、つまり、一致しなかった場合は、captcha_checkルール
// のエラーメッセージを設定し、FALSEを返します。
        if ($row->count == 0) {
            $error = '画像認証コードが一致しません。';

            return false;
        }

        return true;
    }
}
