<?php

declare(strict_types=1);

namespace App\Libraries;

use Kenjis\CI3Compatible\Core\CI_Controller;
use Kenjis\CI3Compatible\Library\CI_Pagination;

class GeneratePagination
{
    /** @var CI_Controller */
    private $CI;

    /** @var CI_Pagination */
    private $pagination;

    public function __construct()
    {
// ページネーションクラスをロードします。
        $this->CI =& get_instance();
        $this->CI->load->library('pagination');
        $this->pagination = $this->CI->pagination; // @phpstan-ignore-line
    }

    /**
     * ページネーションの生成
     *
     * @param array{base_url: string, per_page: int, total_rows: int, uri_segment: int} $config
     */
    public function get_links(array $config): string
    {
        $config['reuse_query_string'] = true;

// $configでページネーションを初期化します。
        $this->pagination->initialize($config);

// 生成したリンクの文字列を返します。
        return $this->pagination->create_links();
    }
}
