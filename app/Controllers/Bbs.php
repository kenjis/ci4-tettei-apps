<?php

declare(strict_types=1);

/* モバイル対応簡易掲示板
 *
 */

namespace App\Controllers;

use App\Models\Bbs\PostForm;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RedirectResponse;
use Kenjis\CI3Compatible\Core\CI_Config;
use Kenjis\CI3Compatible\Core\CI_Controller;
use Kenjis\CI3Compatible\Core\CI_Input;
use Kenjis\CI3Compatible\Database\CI_DB;
use Kenjis\CI3Compatible\Database\CI_DB_result;
use Kenjis\CI3Compatible\Library\CI_Pagination;
use Kenjis\CI3Compatible\Library\CI_User_agent;

use function max;

/**
 * @property CI_DB $db
 * @property CI_User_agent $agent
 * @property CI_Pagination $pagination
 * @property CI_Input $input
 * @property CI_Config $config
 */
class Bbs extends CI_Controller
{
    /** @var IncomingRequest */
    protected $request;

// 記事表示ページで、1ページに表示する記事の件数を設定します。
    /** @var int 1ページに表示する記事の件数 */
    private $limit = 5;

    /** @var string[] */
    protected $helpers = ['form', 'url'];

    /** @var PostForm */
    private $form;

    public function __construct()
    {
        parent::__construct();

        $this->load->library('user_agent');
// データベースを使うため、データベースクラスをロードします。
        $this->load->database();
    }

    /**
     * 日付順に記事を表示
     */
    public function index(string $page = '1'): void
    {
// 引数から$pageに値が渡されます。これは、3番目のURIセグメントの値です。
// ユーザが変更可能なデータですので、int型へ変換し、必ず整数値にします。
        $page = $this->convertToInt($page);

// ページ番号をoffsetに変換します。
        $offset = max($page - 1, 0) * $this->limit;

// 新しい記事ID順に、limit値とoffset値を指定し、bbsテーブルから記事データ
// (オブジェクト)を取得し、$data['query']に代入します。order_by()メソッドは、
// フィールド名とソート順を引数にとり、ORDER BY句を指定します。
        $data = [
            'query' => $this->getPostList($offset),
// ページネーションを生成します。
            'pagination' => $this->createPagination(),
        ];

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

        $config = [
            'base_url' => $this->config->site_url('/bbs/index/'),
// 記事の総件数をbbsテーブルから取得します。count_all()メソッドは、テーブル名
// を引数にとり、そのテーブルのレコード数を返します。
            'total_rows' => $this->db->count_all('bbs'),
            'per_page' => $this->limit,
        ];

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
        $this->showPostPage();
    }

    /**
     * 確認ページ
     */
    public function confirm(): void
    {
        $this->form = new PostForm();

// 検証をパスしなかった場合は、新規投稿ページを表示します。
        if (! $this->validate($this->form->getValidationRules('confirm'))) {
// 投稿されたIDのキャプチャを削除します。
            $this->deleteCaptchaData();

            $this->showPostPage();

            return;
        }

// 検証をパスした場合は、投稿確認ページ(bbs_confirm)を表示します。
        $this->form->setData($this->request->getPost());

        $this->loadView(
            'bbs_confirm',
            ['form' => $this->form]
        );
    }

    /**
     * @return array{name: string, email: string, subject: string, body: string, password: string}
     */
    private function getBasicPostData(): array
    {
        return $this->request->getPost([
            'name',
            'email',
            'subject',
            'body',
            'password',
        ]);
    }

    /**
     * 投稿されたIDのキャプチャを削除
     */
    private function deleteCaptchaData(): void
    {
        $this->db->delete(
            'captcha',
            ['captcha_id' => $this->request->getPost('key')]
        );
    }

    /**
     * 新規投稿ページを表示
     */
    private function showPostPage(): void
    {
// 画像キャプチャを生成します。ランダムな文字列を生成するために文字列ヘルパーを
// ロードし、キャプチャプラグインをロードします。
        $this->load->helper('string');
        $this->load->helper('captcha');

        [$key, $cap] = $this->createCaptcha();

        $data          = $this->getBasicPostData();
        $data['image'] = $cap['image'];
        $data['key']   = $key;

        $this->loadView('bbs_post', $data);
    }

    /**
     * @return array{0: int, 1: array{word: string, time: float, image: string, filename: string}}
     */
    private function createCaptcha(): array
    {
// 画像キャプチャ生成に必要な設定をします。文字列ヘルパーのrandom_string()
// メソッドを使い、ランダムな4桁の数字を取得します。
        $vals = [
            'word'     => random_string('numeric', 4),
            'img_path' => FCPATH . 'captcha/',
            'img_url'  => base_url('captcha'),
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

        return [$key, $cap];
    }

    /**
     * 削除ページ
     */
    public function delete(string $id = ''): void
    {
// 第1引数、つまり、3番目のURIセグメントのデータをint型に変換します。
        $id = $this->convertToInt($id);

// POSTされたpasswordフィールドの値を$passwordに代入します。
        $password = (string) $this->request->getPost('password');

// POSTされたdeleteフィールドの値を$deleteに代入します。この値が
// 1の場合は、削除を実行します。1以外は、削除の確認ページを表示します。
        $delete = (int) $this->request->getPost('delete');

// 削除パスワードが入力されていない場合は、エラーページを表示します。
        if ($password === '') {
            $this->loadView('bbs_delete_error');

            return;
        }

// 記事IDと削除パスワードを条件として、bbsテーブルを検索します。
        $query = $this->getPostToDelete($id, $password);

        // 削除パスワードが一致しなかった場合は、エラーページを表示します。
        if ($query->num_rows() === 0) {
            $this->loadView('bbs_delete_error');

            return;
        }

// レコードが存在した場合は、削除パスワードが一致したことになりますので、
// 次の処理に移ります。
// POSTされたデータのdeleteフィールドが1の場合は、確認ページからのPOSTなの
// で、記事を削除します。
        if ($delete === 1) {
            $this->deletePost($id);
            $this->loadView('bbs_delete_finished');

            return;
        }

// deleteフィールドが1以外の場合は、記事表示ページからのPOSTですので、確認
// ページを表示します。
        $row = $query->row();

        $data = [
            'id'       => $row->id,
            'name'     => $row->name,
            'email'    => $row->email,
            'subject'  => $row->subject,
            'datetime' => $row->datetime,
            'body'     => $row->body,
            'password' => $row->password,
        ];

        $this->loadView('bbs_delete_confirm', $data);
    }

    private function deletePost(int $id): void
    {
        $this->db->where('id', $id);
        $this->db->delete('bbs');
    }

    private function getPostToDelete(int $id, string $password): CI_DB_result
    {
        $this->db->where('id', $id);
        $this->db->where('password', $password);

        return $this->db->get('bbs');
    }

    /**
     * 投稿された記事をデータベースに登録
     */
    public function insert(): ?RedirectResponse
    {
        $this->form = new PostForm();

// 検証にパスした場合は、送られたデータとIPアドレスをbbsテーブルに登録します。
        if ($this->validate($this->form->getValidationRules())) {
            $data = $this->form->setData($this->request->getPost())->asArray();
            $data['ip_address'] = $this->request->getServer('REMOTE_ADDR');

            $this->insertToDb($data);

// 投稿されたIDのキャプチャを削除します。
            $this->deleteCaptchaData();

// URLヘルパーのredirect()メソッドで記事表示ページにリダイレクトします。
            return redirect()->to('/bbs');
        }

// 検証にパスしない場合は、新規投稿ページを表示します。
// 投稿されたIDのキャプチャを削除します。
        $this->deleteCaptchaData();

        $this->showPostPage();

        return null;
    }

    /**
     * @param array<string, string> $data
     */
    private function insertToDb(array $data): void
    {
        $this->db->insert('bbs', $data);
    }

    /**
     * 携帯端末かどうかを判定し、ビューをロード
     *
     * @param array<string, mixed> $data
     */
    private function loadView(string $file, array $data = []): void
    {
// 携帯端末の場合は、「_mobile」がファイル名に付くビューファイルをロードします。
        if ($this->agent->is_mobile()) {
            $this->load->view($file . '_mobile', $data);

            return;
        }

        $this->load->view($file, $data);
    }
}
