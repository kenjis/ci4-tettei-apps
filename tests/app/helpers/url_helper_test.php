<?php

declare(strict_types=1);

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
        $expected = 'http://localhost/CodeIgniter/images/icons/simle.jpg';
        $this->assertEquals($expected, $acutual);

        $acutual = base_url('"><s>abc</s><a name="test');
        $expected = 'http://localhost/CodeIgniter/"><s>abc</s><a name="test';
        $this->assertEquals($expected, $acutual);
    }

    public function test_site_url(): void
    {
        $actual = site_url('welcome');
        $expected = 'http://localhost/CodeIgniter/welcome';
        $this->assertEquals($expected, $actual);

        $actual = site_url('"><s>abc</s><a name="test');
        $expected = 'http://localhost/CodeIgniter/"><s>abc</s><a name="test';
        $this->assertEquals($expected, $actual);
    }

    public function test_anchor(): void
    {
        $actual = anchor('news/local/123', 'My News', ['title' => 'The best news!']);
        $expected = '<a href="http://localhost/CodeIgniter/news/local/123" title="The best news!">My News</a>';
        $this->assertEquals($expected, $actual);

        $actual = anchor('news/local/123', '<s>abc</s>', ['<s>name</s>' => '<s>val</s>']);
        $expected = '<a href="http://localhost/CodeIgniter/news/local/123" <s>name</s>="<s>val</s>"><s>abc</s></a>';
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
