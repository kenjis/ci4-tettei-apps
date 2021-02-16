<?php

declare(strict_types=1);

namespace App\Controllers;

use Kenjis\CI3Compatible\Test\TestCase\FeatureTestCase;
use Kenjis\CI3Compatible\Test\Traits\UnitTest;
use Symfony\Component\DomCrawler\Crawler;

use function html_escape;
use function time;
use function trim;

class Bbs_test extends FeatureTestCase
{
    use UnitTest;

    public function setUp(): void
    {
        parent::setUp();

        $this->request->setCallable(
            static function ($CI): void {
                $CI->load->library('user_agent');
            }
        );
    }

    public function test_index(): void
    {
        $output = $this->request('GET', 'bbs/index');
        $this->assertStringContainsString('<title>掲示板</title>', $output);
    }

    public function test_index_mobile(): void
    {
        $agent = $this->getDouble('CI_User_agent', ['is_mobile' => true]);
        $this->request->setCallable(
            static function ($CI) use ($agent): void {
                $CI->agent = $agent;
            }
        );

        $output = $this->request('GET', 'bbs/index');
        $this->assertStringContainsString('<title>ﾓﾊﾞｲﾙ掲示板</title>', $output);
    }

    public function test_post(): void
    {
        $output = $this->request('GET', 'bbs/post');
        $this->assertStringContainsString('<title>掲示板: 新規投稿</title>', $output);
    }

    public function test_confirm_error(): void
    {
        $output = $this->request(
            'POST',
            'bbs/confirm',
            ['name' => '']
        );
        $this->assertStringContainsString('名前 は必須項目です', $output);
    }

    public function test_confirm_ok(): void
    {
        $output = $this->request(
            'POST',
            'bbs/confirm',
            [
                'name' => '<s>abc</s>',
                'email' => 'test@example.jp',
                'subject' => '<s>abc</s>',
                'body' => '<s>abc</s>',
                'password' => '<s>abc</s>',
                'captcha' => '8888',
                'key' => '139',
            ]
        );
        $this->assertStringContainsString('投稿確認', $output);
    }

    public function test_insert_ok(): void
    {
        $subject = '<s>xyz</s> ' . time();
        $output = $this->request(
            'POST',
            'bbs/insert',
            [
                'name' => '<s>xyz</s>',
                'email' => 'test@example.jp',
                'subject' => $subject,
                'body' => '<s>xyz</s>',
                'password' => '<s>xyz</s>',
                'captcha' => '8888',
                'key' => '139',
            ]
        );
        $this->assertRedirect('bbs', 302);

        $output = $this->request('GET', 'bbs/index');
        $this->assertStringContainsString(html_escape($subject), $output);
    }

    public function test_delete(): void
    {
        $this->request(
            'POST',
            'bbs/insert',
            [
                'name' => '削除太郎',
                'email' => 'test@example.jp',
                'subject' => '削除する投稿',
                'body' => 'この投稿を削除します。',
                'password' => 'delete',
                'captcha' => '8888',
                'key' => '139',
            ]
        );
        $this->assertRedirect('bbs', 302);

        $output = $this->request('GET', 'bbs/index');
        $crawler = new Crawler($output);

        // 最初の <h1><a>〜</a></h1> のテキストを取得
        $text = $crawler->filter('h1 > a')->eq(0)->text();
        $id = trim($text, '[]');

        $output = $this->request('POST', "bbs/delete/$id");
        $this->assertStringContainsString('記事を削除できませんでした', $output);

        $output = $this->request(
            'POST',
            "bbs/delete/$id",
            ['password' => 'delete']
        );
        $this->assertStringContainsString('削除の確認', $output);

        $output = $this->request(
            'POST',
            "bbs/delete/$id",
            [
                'password' => 'bad password',
                'delete' => '1',
            ]
        );
        $this->assertStringContainsString('記事を削除できませんでした', $output);

        $output = $this->request(
            'POST',
            "bbs/delete/$id",
            [
                'password' => 'delete',
                'delete' => '1',
            ]
        );
        $this->assertStringContainsString('記事の削除完了', $output);
    }
}
