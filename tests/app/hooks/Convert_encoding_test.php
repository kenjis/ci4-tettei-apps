<?php

declare(strict_types=1);

use Kenjis\CI3Compatible\Test\TestCase\TestCase;
class Convert_encoding_test extends TestCase
{
    /** @var ConvertEncoding */
    private $obj;

    public function setUp(): void
    {
        require_once APPPATH . 'hooks/Convert_encoding.php';
        $this->obj = new Convert_encoding();
    }

    public function test_run_and_add_agent(): void
    {
        reset_instance();

        $str = '尾骶骨';
        $_SERVER['PATH_INFO'] = '/bbs';
        $_POST = [
            'name' => mb_convert_encoding($str, 'SJIS-win', 'UTF-8'),
            'email' => '',
        ];
        $agent = $this->getDouble('CI_User_agent', ['is_mobile' => true]);
        load_class_instance('User_agent', $agent);
        // is_cli()の返り値をfalseに変更
        set_is_cli(false);

        $this->obj->run();
        $this->assertEquals('尾骨', $_POST['name']);

        new CI_Controller();

        $this->obj->add_agent();
        $CI =& get_instance();
        $this->assertSame($agent, $CI->agent);
        $this->assertFalse(isset($CI->user_agent));

        // is_cli()の返り値をtrueに戻す
        set_is_cli(true);
    }

    public function test_check_route_false(): void
    {
        reset_instance();
        set_is_cli(false);
        $_SERVER['PATH_INFO'] = '/shop';

        $this->obj->run();
        $loaded_classes = is_loaded();
        $this->assertFalse(isset($loaded_classes['User_agent']));

        $this->obj->add_agent();
        $this->assertFalse(isset($CI->agent));

        set_is_cli(true);
        new CI_Controller();
    }
}
