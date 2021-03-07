<?php

declare(strict_types=1);

namespace App\Controllers;

use Kenjis\CI3Compatible\Core\CI_Config;
use Kenjis\CI3Compatible\Core\CI_Input;
use Kenjis\CI3Compatible\Database\CI_DB;
use Kenjis\CI3Compatible\Library\CI_Form_validation;
use Kenjis\CI3Compatible\Library\CI_Pagination;
use Kenjis\CI3Compatible\Library\CI_User_agent;

use function get_instance;
use function time;

/**
 * @property CI_DB $db
 * @property CI_User_agent $agent
 * @property CI_Pagination $pagination
 * @property CI_Form_validation $form_validation
 * @property CI_Input $input
 * @property CI_Config $config
 */
class BbsValidationRules
{
// キャプチャの検証をするメソッドです。バリデーション(認証)クラスより呼ばれます。
    public function captcha_check(string $str, ?string &$error): bool
    {
// 環境がtestingまたはacceptanceの場合は、キャプチャの検証をスキップします。
        if (ENVIRONMENT === 'testing' || ENVIRONMENT === 'acceptance') { // @phpstan-ignore-line
            if ($str === '8888') {
                return true;
            }
        }

        $CI = get_instance();
        /** @var CI_DB */
        $db = $CI->db; // @phpstan-ignore-line

// 有効期限を2時間に設定し、それ以前に生成されたキャプチャをデータベースから
// 削除します。delete()メソッドの第2引数では、「captcha_time <」を配列のキーに
// していますが、このように記述することで、WHERE句の条件の演算子を指定できます。
        $expiration = time() - 7200;    // 有効期限 2時間
        $db->delete('captcha', ['captcha_time <' => $expiration]);

// バリデーション(検証)クラスより引数$strに渡された、ユーザからの入力値がデータ
// ベースに保存されている値と一致するかどうかを調べます。隠しフィールドである
// keyフィールドの値と$strを条件に、有効期限内のレコードをデータベースから
// 検索します。条件に合うレコードが存在すれば、一致したと判断します。
// where()メソッドは、複数回呼ばれると、AND条件になります。
        $db->select('COUNT(*) AS count');
        $db->where('word', $str);
        // @phpstan-ignore-next-line
        $db->where('captcha_id', $CI->input->post('key'));
        $db->where('captcha_time >', $expiration);
        $query = $db->get('captcha');
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
