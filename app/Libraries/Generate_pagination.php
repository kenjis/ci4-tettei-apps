<?php

declare(strict_types=1);

namespace App\Libraries;

class Generate_pagination
{
    private $CI;

    // ページネーションの生成
    public function get_links(string $path, int $total, int $uri_segment): string
    {
// ページネーションクラスをロードします。
        $this->CI =& get_instance();
        $this->CI->load->library('pagination');

        $config = [];
// リンク先のURLを指定します。
        $config['base_url'] = $this->CI->config->site_url($path);
// 総件数を指定します。
        $config['total_rows'] = $total;
// 1ページに表示する件数を指定します。
        $config['per_page'] = $this->CI->limit;
// ページ番号情報がどのURIセグメントに含まれるか指定します。
        $config['uri_segment'] = $uri_segment;
// ページネーションでクエリ文字列を使えるようにします。
        $config['reuse_query_string'] = true;
// $configでページネーションを初期化します。
        $this->CI->pagination->initialize($config);

// 生成したリンクの文字列を返します。
        return $this->CI->pagination->create_links();
    }
}
