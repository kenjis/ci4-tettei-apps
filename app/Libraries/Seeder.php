<?php

declare(strict_types=1);

namespace App\Libraries;

use Config\Database;

class Seeder
{
    private $CI;
    protected $db;
    protected $dbforge;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->load->dbforge();
        $this->db = $this->CI->db;
        $this->dbforge = $this->CI->dbforge;
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
        $obj->run();
    }

    public function __get($property)
    {
        return $this->CI->$property;
    }
}
