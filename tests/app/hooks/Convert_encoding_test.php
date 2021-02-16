<?php

declare(strict_types=1);

use App\Filters\ConvertEncoding;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\URI;
use CodeIgniter\HTTP\UserAgent;
use Config\App;
use Kenjis\CI3Compatible\Test\TestCase\TestCase;

class Convert_encoding_test extends TestCase
{
    /** @var ConvertEncoding */
    private $obj;

    public function setUp(): void
    {
        $this->obj = new ConvertEncoding();
    }

    public function test_before(): void
    {
        $str = '尾骶骨';
        $_POST = [
            'name' => mb_convert_encoding($str, 'SJIS-win', 'UTF-8'),
            'email' => '',
        ];
        $userAgent = $this->getDouble(
            UserAgent::class,
            ['isMobile' => true]
        );
        $request = new IncomingRequest(new App(), new URI(), null, $userAgent);

        $this->obj->before($request);

        $name = $request->getPost('name');
        $this->assertEquals('尾?骨', $name);
    }

    public function test_after(): void
    {
        $str = '尾骶骨';
        $_POST = [
            'name' => mb_convert_encoding($str, 'SJIS-win', 'UTF-8'),
            'email' => '',
        ];
        $userAgent = $this->getDouble(
            UserAgent::class,
            ['isMobile' => true]
        );
        $request = new IncomingRequest(new App(), new URI(), null, $userAgent);

        $response = new Response(new App());
        $response->setBody($str);

        $this->obj->after($request, $response);

        $body = $response->getBody();
        $this->assertEquals('尾?骨', mb_convert_encoding($body, 'UTF-8', 'SJIS-win'));
    }
}
