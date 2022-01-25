<?php

declare(strict_types=1);

namespace App\Libraries;

use Config\Database;
use Kenjis\CI3Compatible\Core\CI_Controller;
use Kenjis\CI3Compatible\Database\CI_DB;
use Kenjis\CI3Compatible\Database\CI_DB_forge;
use Kenjis\CI3Compatible\Library\Seeder as CI3Seeder;

use function assert;

class Seeder
{
    /** @var CI_Controller */
    private $CI;

    /** @var CI_DB */
    protected $db;

    /** @var CI_DB_forge */
    protected $dbforge;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->dbforge();
        $this->db = $this->CI->db;           // @phpstan-ignore-line
        $this->dbforge = $this->CI->dbforge; // @phpstan-ignore-line
    }

    /**
     * Run another seeder
     *
     * @param string $seeder Seeder classname
     */
    public function call(string $seeder): void
    {
        $file = APPPATH . 'Database/Seeds/' . $seeder . '.php';
        require_once $file;
        $seeder = 'App\\Database\\Seeds\\' . $seeder;

        $obj = new $seeder(new Database());
        assert($obj instanceof CI3Seeder);

        $obj->run();
    }

    public function __get(string $property): object
    {
        return $this->CI->$property;
    }
}
