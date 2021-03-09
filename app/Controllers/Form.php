<?php

declare(strict_types=1);

/* コンタクトフォーム
 *
 */

namespace App\Controllers;

use CodeIgniter\HTTP\IncomingRequest;
use Kenjis\CI3Compatible\Core\CI_Controller;
use Kenjis\CI3Compatible\Core\CI_Input;
use Kenjis\CI3Compatible\Core\CI_Output;
use Kenjis\CI3Compatible\Library\CI_Email;
use Kenjis\CI3Compatible\Library\CI_Form_validation;
use Kenjis\CI3Compatible\Library\CI_Session;

/**
 * @property CI_Email $email
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property CI_Input $input
 * @property CI_Output $output
 */
class Form extends CI_Controller
{
    /** @var IncomingRequest */
    protected $request;

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

    public function __construct()
    {
// 親クラスのコンストラクタを呼び出します。コントローラにコンストラクタを
// 記述する場合は、忘れずに記述してください。
        parent::__construct();

// 必要なヘルパーをロードします。
        $this->load->helper(['form', 'url']);

// セッションクラスをロードすることで、セッションを開始します。
        $this->load->library('session');

// バリデーション(検証)クラスをロードします。
        $this->load->library('form_validation');
    }

    public function index(): void
    {
// 入力ページ(form)のビューをロードし表示します。
        $this->load->view('form');
    }

    public function confirm(): void
    {

// バリデーション(検証)クラスのrun()メソッドを呼び出し、送信されたデータの検証
// を行います。検証OKなら、確認ページ(form_confirm)を表示します。
        if ($this->validate($this->validationRules)) {
            $data = [
                'name' => trim($this->request->getPost('name')),
                'email' => trim($this->request->getPost('email')),
                'comment' => $this->request->getPost('comment'),
            ];

            $this->load->view('form_confirm', $data);

            return;
        }

// 検証でエラーの場合、入力ページ(form)を表示します。
        $this->load->view('form');
    }

    public function send(): void
    {

// 送信されたデータの検証を行い、検証でエラーの場合、入力ページ(form)を表示します。
        if (! $this->validate($this->validationRules)) {
            $this->load->view('form');

            return;
        }

// 検証OKなら、メールを送信します。
// メールの内容を設定します。
        $mail = [
            'from_name' => $this->request->getPost('name'),
            'from' => $this->request->getPost('email'),
            'to' => 'info@example.jp',
            'subject' => 'コンタクトフォーム',
            'body' => $this->request->getPost('comment'),
        ];

// sendmail()メソッドを呼び出しメールの送信処理を行います。
// メールの送信に成功したら、完了ページ(form_end)を表示します。
        if ($this->sendmail($mail)) {
// 完了ページ(form_end)を表示し、セッションを破棄します。
            $this->load->view('form_end');
            $this->session->sess_destroy();

            return;
        }

// メールの送信に失敗した場合、エラーを表示します。
        echo 'メール送信エラー';
    }

    /**
     * @param array{from_name: string, from: string, to: string, subject: string, body: string} $mail
     */
    private function sendmail(array $mail): bool
    {
// Emailクラスをロードします。
        $this->load->library('email');

        $config = [
// メールの送信方法を指定します。ここでは、mail()関数を使います。
            'protocol' => 'mail',
// 日本語ではワードラップ機能は使えませんのでオフにします。
            'wordwrap' => false,
        ];
// $configでEmailクラスを初期化します。
        $this->email->initialize($config);

// メールの内容を変数に代入します。
        $fromName = $mail['from_name'];
        $from     = $mail['from'];
        $to       = $mail['to'];
        $subject  = $mail['subject'];
        $body     = $mail['body'];

// 差出人、あて先、件名、本文をEmailクラスに設定します。
        $this->email->from($from, $fromName);
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($body);

// Emailクラスのsend()メソッドで、実際にメールを送信します。
// メールの送信が成功した場合はTRUEを、失敗した場合はFALSEを返します。
        if ($this->email->send()) {
            return true;
        }

        // echo $this->email->print_debugger();
        return false;
    }
}
