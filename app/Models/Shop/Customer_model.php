<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Core\CI_Model;
use Kenjis\CI3Compatible\Library\CI_Session;

/**
 * @property CI_Session $session
 */
class Customer_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');
    }

    /**
     * @param array $data
     */
    public function set(array $data): void
    {
        foreach ($data as $key => $val) {
            $this->session->set_userdata($key, $val);
        }
    }

    /**
     * @return array
     */
    public function get(): array
    {
        return [
            'name'  => $this->session->userdata('name'),
            'zip'   => $this->session->userdata('zip'),
            'addr'  => $this->session->userdata('addr'),
            'tel'   => $this->session->userdata('tel'),
            'email' => $this->session->userdata('email'),
        ];
    }
}
