<?php

declare(strict_types=1);

/* コンタクトフォーム
 *
 */

namespace App\Controllers;

use App\Models\Form\FormForm;
use CodeIgniter\HTTP\IncomingRequest;
use Kenjis\CI3Compatible\Core\CI_Controller;
use Kenjis\CI3Compatible\Exception\RuntimeException;
use Kenjis\CI3Compatible\Library\CI_Email;
use Kenjis\CI3Compatible\Library\CI_Session;

/**
 * @property CI_Email $email
 * @property CI_Session $session
 */
class Form extends CI_Controller
{
    /** @var IncomingRequest */
    protected $request;

// 必要なヘルパーをロードします。
    /** @var string[] */
    protected $helpers = ['form', 'url'];

    /** @var FormForm */
    private $form;

    public function __construct()
    {
// 親クラスのコンストラクタを呼び出します。コントローラにコンストラクタを
// 記述する場合は、忘れずに記述してください。
        parent::__construct();

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
        if ($this->request->getMethod() !== 'post') {
            throw new RuntimeException('不正な入力です。', 400);
        }

        $this->form = new FormForm($this->request->getPost(
            ['name', 'email', 'comment']
        ));

// バリデーション(検証)クラスのrun()メソッドを呼び出し、送信されたデータの検証
// を行います。検証OKなら、確認ページ(form_confirm)を表示します。
        if ($this->validate($this->form->getValidationRules())) {
            $this->load->view('form_confirm', $this->form->asArray());

            return;
        }

// 検証でエラーの場合、入力ページ(form)を表示します。
        $this->load->view('form');
    }

    public function send(): void
    {
        if ($this->request->getMethod() !== 'post') {
            throw new RuntimeException('不正な入力です。', 400);
        }

        $this->form = new FormForm($this->request->getPost(
            ['name', 'email', 'comment']
        ));

// 送信されたデータの検証を行い、検証でエラーの場合、入力ページ(form)を表示します。
        if (! $this->validate($this->form->getValidationRules())) {
            $this->load->view('form');

            return;
        }

// 検証OKなら、メールを送信します。
// メールの内容を設定します。
        $mail = [
            'from_name' => $this->form->getName(),
            'from' => $this->form->getEmail(),
            'to' => 'info@example.jp',
            'subject' => 'コンタクトフォーム',
            'body' => $this->form->getComment(),
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
