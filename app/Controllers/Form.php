<?php

declare(strict_types=1);

/* コンタクトフォーム
 *
 */

namespace App\Controllers;

use App\Libraries\Validation\FormValidation;
use App\Models\Form\FormForm;
use CodeIgniter\HTTP\IncomingRequest;
use Config\Services;
use Kenjis\CI3Compatible\Library\CI_Email;
use Kenjis\CI4\AttributeRoutes\Route;

/**
 * @property CI_Email $email
 */
class Form extends MyController
{
    /** @var IncomingRequest */
    protected $request;

// 必要なヘルパーをロードします。
    /** @var string[] */
    protected $helpers = ['form', 'url'];

    public function __construct()
    {
// 親クラスのコンストラクタを呼び出します。コントローラにコンストラクタを
// 記述する場合は、忘れずに記述してください。
        parent::__construct();

// Emailクラスをロードします。
        $this->load->library('email');
    }

    #[Route('form', methods: ['get', 'post'])]
    public function index(): void
    {
// 入力ページ(form)のビューをロードし表示します。
        $this->load->view('form');
    }

    #[Route('form/confirm', methods: ['post'])]
    public function confirm(): void
    {
        $this->postOnly();

        $form = new FormForm();
        $formValidation = new FormValidation(Services::validation());

// バリデーション(検証)クラスのrun()メソッドを呼び出し、送信されたデータの検証
// を行います。検証OKなら、確認ページ(form_confirm)を表示します。
        if ($formValidation->validate($this->request, $form)) {
            $this->load->view(
                'form_confirm',
                ['form' => $form]
            );

            return;
        }

// 検証でエラーの場合、入力ページ(form)を表示します。
        $this->load->view('form');
    }

    #[Route('form/send', methods: ['post'])]
    public function send(): void
    {
        $this->postOnly();

        $form = new FormForm();
        $formValidation = new FormValidation(Services::validation());

// 送信されたデータの検証を行い、検証でエラーの場合、入力ページ(form)を表示します。
        if (! $formValidation->validate($this->request, $form)) {
            $this->load->view('form');

            return;
        }

// 検証OKなら、メールを送信します。
// メールの内容を設定します。
        $mail = [
            'from_name' => $form->getName(),
            'from' => $form->getEmail(),
            'to' => 'info@example.jp',
            'subject' => 'コンタクトフォーム',
            'body' => $form->getComment(),
        ];

// sendmail()メソッドを呼び出しメールの送信処理を行います。
// メールの送信に成功したら、完了ページ(form_end)を表示します。
        if ($this->sendmail($mail)) {
// 完了ページ(form_end)を表示します。
            $this->load->view('form_end');

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
        $config = [
// メールの送信方法を指定します。ここでは、mail()関数を使います。
            'protocol' => 'mail',
// 日本語ではワードラップ機能は使えませんのでオフにします。
            'wordwrap' => false,
        ];
// $configでEmailクラスを初期化します。
        $this->email->initialize($config);

// 差出人、あて先、件名、本文をEmailクラスに設定します。
        $this->email->from($mail['from'], $mail['from_name']);
        $this->email->to($mail['to']);
        $this->email->subject($mail['subject']);
        $this->email->message($mail['body']);

// Emailクラスのsend()メソッドで、実際にメールを送信します。
// メールの送信が成功した場合はTRUEを、失敗した場合はFALSEを返します。
        if ($this->email->send()) {
            return true;
        }

        // echo $this->email->print_debugger();
        return false;
    }
}
