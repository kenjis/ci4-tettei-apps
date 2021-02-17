<?php

declare(strict_types=1);

/* モバイル対応簡易掲示板
 *
 */

namespace App\Controllers;

use Kenjis\CI3Compatible\Core\CI_Config;
use Kenjis\CI3Compatible\Core\CI_Controller;
use Kenjis\CI3Compatible\Core\CI_Input;
use Kenjis\CI3Compatible\Database\CI_DB_query_builder;
use Kenjis\CI3Compatible\Database\CI_DB_result;
use Kenjis\CI3Compatible\Library\CI_Form_validation;
use Kenjis\CI3Compatible\Library\CI_Pagination;
use Kenjis\CI3Compatible\Library\CI_User_agent;

use function max;

/**
 * @property CI_DB_query_builder $db
 * @property CI_User_agent $agent
 * @property CI_Pagination $pagination
 * @property CI_Form_validation $form_validation
 * @property CI_Input $input
 * @property CI_Config $config
 */
class Bbs extends CI_Controller
{
// 記事表示ページで、1ページに表示する記事の件数を設定します。
    /** @var int 1ページに表示する記事の件数 */
    public $limit = 5;

    public function __construct()
    {
        parent::__construct();

        $this->load->library('user_agent');
        $this->load->helper(['form', 'url']);
// データベースを使うため、データベースクラスをロードします。
        $this->load->database();
    }

    /**
     * // 日付順に記事を表示
     */
    public function index(string $page = '1'): void
    {
// 引数から$pageに値が渡されます。これは、3番目のURIセグメントの値です。
// ユーザが変更可能なデータですので、int型へ変換し、必ず整数値にします。
        $page = (int) $page;

// ページ番号をoffsetに変換します。
        $offset = max($page - 1, 0) * $this->limit;

// 新しい記事ID順に、limit値とoffset値を指定し、bbsテーブルから記事データ
// (オブジェクト)を取得し、$data['query']に代入します。order_by()メソッドは、
// フィールド名とソート順を引数にとり、ORDER BY句を指定します。
        $data = [];
        $data['query'] = $this->getPostList($offset);

// ページネーションを生成します。
        $data['pagination'] = $this->createPagination();

// _load_view()メソッドは、携帯端末かどうかで、読み込むビューファイルを
// 切り替えするためのメソッドです。
        $this->loadView('bbs_show', $data);
    }

    private function getPostList(int $offset): CI_DB_result
    {
        $this->db->order_by('id', 'desc');

        return $this->db->get('bbs', $this->limit, $offset);
    }

    private function createPagination(): string
    {
        $this->load->library('pagination');

        $config = [];
        $config['base_url'] = $this->config->site_url('/bbs/index/');
// 記事の総件数をbbsテーブルから取得します。count_all()メソッドは、テーブル名
// を引数にとり、そのテーブルのレコード数を返します。
        $config['total_rows'] = $this->db->count_all('bbs');
        $config['per_page'] = $this->limit;
        $this->pagination->initialize($config);

        return $this->pagination->create_links();
    }

    /**
     * 新規投稿ページ
     */
    public function post(): void
    {
// バリデーションを設定し、新規投稿ページを表示します。実際の処理は、他でも
// 使いますので、プライベートメソッドにしています。
        $this->setValidation();
        $this->showPostPage();
    }

    /**
     * 確認ページ
     */
    public function confirm(): void
    {
// 検証ルールを設定します。
        $this->setValidation();

// 検証をパスしなかった場合は、新規投稿ページを表示します。検証をパスした場合
// は、投稿確認ページ(bbs_confirm)を表示します。
        if ($this->form_validation->run() == false) {
// 投稿されたIDのキャプチャを削除します。
            $this->deleteCaptchaData();

            $this->showPostPage();
        } else {
            $data = [];
            $data['name']       = $this->input->post('name');
            $data['email']      = $this->input->post('email');
            $data['subject']    = $this->input->post('subject');
            $data['body']       = $this->input->post('body');
            $data['password']   = $this->input->post('password');
            $data['key']        = $this->input->post('key');
            $data['captcha']    = $this->input->post('captcha');
            $this->loadView('bbs_confirm', $data);
        }
    }

// 投稿されたIDのキャプチャを削除します。
    private function deleteCaptchaData(): void
    {
        $this->db->delete('captcha', ['captcha_id' => $this->input->post('key')]);
    }

// 新規投稿ページを表示します。
    private function showPostPage(): void
    {
// 画像キャプチャを生成します。ランダムな文字列を生成するために文字列ヘルパーを
// ロードし、キャプチャプラグインをロードします。
        $this->load->helper('string');
        $this->load->helper('captcha');
// 画像キャプチャ生成に必要な設定をします。文字列ヘルパーのrandom_string()
// メソッドを使い、ランダムな4桁の数字を取得します。
        $vals = [
            'word'      => random_string('numeric', 4),
            'img_path'  => FCPATH . 'captcha/',
            'img_url'   => base_url_() . 'captcha/',
        ];
        $cap = create_captcha($vals);
        $data = [
            'captcha_id'   => '',
            'captcha_time' => $cap['time'],
            'word'         => $cap['word'],
        ];
// 生成したキャプチャの情報をcaptchaテーブルに登録します。
        $this->db->insert('captcha', $data);
// 登録時に付けられたキャプチャのID番号を取得します。
        $key = $this->db->insert_id();

        $data['image']      = $cap['image'];
        $data['key']        = $key;
        $data['name']       = $this->input->post('name');
        $data['email']      = $this->input->post('email');
        $data['subject']    = $this->input->post('subject');
        $data['body']       = $this->input->post('body');
        $data['password']   = $this->input->post('password');
        $this->loadView('bbs_post', $data);
    }

    /**
     * 削除ページ
     */
    public function delete(string $id = ''): void
    {
// 第1引数、つまり、3番目のURIセグメントのデータをint型に変換します。
        $id = (int) $id;
// POSTされたpasswordフィールドの値を$passwordに代入します。
        $password = $this->input->post('password');
// POSTされたdeleteフィールドの値を$deleteに代入します。この値が
// 1の場合は、削除を実行します。1以外は、削除の確認ページを表示します。
        $delete = (int) $this->input->post('delete');

// 削除パスワードが入力されていない場合は、エラーページを表示します。
        if ($password == '') {
            $this->loadView('bbs_delete_error');

            return;
        }

// 記事IDと削除パスワードを条件として、bbsテーブルを検索します。
        $this->db->where('id', $id);
        $this->db->where('password', $password);
        $query = $this->db->get('bbs');

        // 削除パスワードが一致しなかった場合は、エラーページを表示します。
        if ($query->num_rows() === 0) {
            $this->loadView('bbs_delete_error');

            return;
        }

// レコードが存在した場合は、削除パスワードが一致したことになりますので、
// 次の処理に移ります。
// POSTされたデータのdeleteフィールドが1の場合は、確認ページからのPOSTなの
// で、記事を削除します。
        if ($delete == 1) {
            $this->db->where('id', $id);
            $this->db->delete('bbs');
            $this->loadView('bbs_delete_finished');

            return;
        }

// deleteフィールドが1以外の場合は、記事表示ページからのPOSTですので、確認
// ページを表示します。
        $row = $query->row();

        $data = [];
        $data['id']       = $row->id;
        $data['name']     = $row->name;
        $data['email']    = $row->email;
        $data['subject']  = $row->subject;
        $data['datetime'] = $row->datetime;
        $data['body']     = $row->body;
        $data['password'] = $row->password;
        $this->loadView('bbs_delete_confirm', $data);
    }

// バリデーションを設定します。
    private function setValidation(): void
    {
        $this->load->library('form_validation');

// 整形・検証ルールを設定します。alpha_numericは英数字のみ、numericは数字のみ
// となります。captcha_checkは、ユーザが定義したcaptcha_check()メソッド
// で検証することを意味します。
        $this->form_validation->set_rules(
            'name',
            '名前',
            'trim|required|max_length[16]'
        );
        $this->form_validation->set_rules(
            'email',
            'メールアドレス',
            'trim|permit_empty|valid_email|max_length[64]'
        );
        $this->form_validation->set_rules(
            'subject',
            '件名',
            'trim|required|max_length[32]'
        );
        $this->form_validation->set_rules(
            'body',
            '内容',
            'trim|required|max_length[200]'
        );
        $this->form_validation->set_rules(
            'password',
            '削除パスワード',
            'max_length[32]'
        );
        $this->form_validation->set_rules(
            'captcha',
            '画像認証コード',
            'trim|required|alpha_numeric|captcha_check'
        );
// keyフィールドは、キャプチャのID番号です。隠しフィールドに仕込まれるのみで
// ユーザの目に触れることはありません。
        $this->form_validation->set_rules('key', 'key', 'numeric');
    }

// 投稿された記事をデータベースに登録します。
    public function insert()
    {
// 検証ルールを設定します。
        $this->setValidation();

// 検証にパスした場合は、送られたデータとIPアドレスをbbsテーブルに登録します。
        if ($this->form_validation->run()) {
            $this->insertToDb();

// 投稿されたIDのキャプチャを削除します。
            $this->deleteCaptchaData();

// URLヘルパーのredirect()メソッドで記事表示ページにリダイレクトします。
            return redirect()->to('/bbs');
        }

// 検証にパスしない場合は、新規投稿ページを表示します。
// 投稿されたIDのキャプチャを削除します。
        $this->deleteCaptchaData();

        $this->showPostPage();
    }

    private function insertToDb(): void
    {
        $data = [];
        $data['name']       = $this->input->post('name');
        $data['email']      = $this->input->post('email');
        $data['subject']    = $this->input->post('subject');
        $data['body']       = $this->input->post('body');
        $data['password']   = $this->input->post('password');
        $data['ip_address'] = $this->input->server('REMOTE_ADDR');
        $this->db->insert('bbs', $data);
    }

// 携帯端末かどうかを判定し、ビューをロードするプライベートメソッドです。
    private function loadView(string $file, array $data = []): void
    {
// 携帯端末の場合は、「_mobile」がファイル名に付くビューファイルをロードします。
        if ($this->agent->is_mobile()) {
            $this->load->view($file . '_mobile', $data);
        } else {
            $this->load->view($file, $data);
        }
    }
}
