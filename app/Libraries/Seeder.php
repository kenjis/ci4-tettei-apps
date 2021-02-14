<?php

declare(strict_types=1);

namespace App\Libraries;

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
        $file = APPPATH . 'database/seeds/' . $seeder . '.php';
        require_once $file;
        $obj = new $seeder();
        $obj->run();
    }

    public function __get($property)
    {
        return $this->CI->$property;
    }
}
