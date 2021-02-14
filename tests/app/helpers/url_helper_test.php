<?php

declare(strict_types=1);

namespace App\Helper;

use Tests\Support\TestCase;

use function get_instance;

class url_helper_test extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $CI =& get_instance();
        $CI->load->helper('url_helper');
    }

    public function test_base_url(): void
    {
        $acutual = base_url('images/icons/simle.jpg');
        $expected = 'http://localhost:8080/images/icons/simle.jpg';
        $this->assertEquals($expected, $acutual);

        $acutual = base_url('"><s>abc</s><a name="test');
        $expected = 'http://localhost:8080/%22%3E%3Cs%3Eabc%3C/s%3E%3Ca%20name=%22test';
        $this->assertEquals($expected, $acutual);
    }

    public function test_site_url(): void
    {
        $actual = site_url('welcome');
        $expected = 'http://localhost:8080/welcome';
        $this->assertEquals($expected, $actual);

        $actual = site_url('"><s>abc</s><a name="test');
        $expected = 'http://localhost:8080/%22%3E%3Cs%3Eabc%3C/s%3E%3Ca%20name=%22test';
        $this->assertEquals($expected, $actual);
    }

    public function test_anchor(): void
    {
        $actual = anchor('news/local/123', 'My News', ['title' => 'The best news!']);
        $expected = '<a href="http://localhost:8080/news/local/123" title="The&#x20;best&#x20;news&#x21;">My News</a>';
        $this->assertEquals($expected, $actual);

        $actual = anchor('news/local/123', '<s>abc</s>', ['<s>name</s>' => '<s>val</s>']);
        $expected = '<a href="http://localhost:8080/news/local/123" <s>name</s>="&lt;s&gt;val&lt;&#x2F;s&gt;"><s>abc</s></a>';
        $this->assertEquals($expected, $actual);
    }

//  public function test_current_url()
//  {
//  }
//
//  public function test_uri_string()
//  {
//  }
//
//  public function test_index_page()
//  {
//  }
//
//  public function test_anchor_popup()
//  {
//  }
//
//  public function test_mailto()
//  {
//  }
//
//  public function test_safe_mailto()
//  {
//  }
//
//  public function test_auto_link()
//  {
//  }
//
//  public function test_prep_url()
//  {
//  }
//
//  public function test_url_title()
//  {
//  }
//
//  public function test_redirect()
//  {
//  }
}
