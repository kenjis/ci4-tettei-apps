<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Core\CI_Loader;
use Kenjis\CI3Compatible\Core\CI_Model;
use Kenjis\CI3Compatible\Library\CI_Session;

/**
 * @property CI_Session $session
 * @property CI_Loader $load
 */
class CustomerModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');
    }

    public function set(CustomerInfoForm $data): void
    {
        foreach ($data as $key => $val) {
            $this->session->set_userdata($key, $val);
        }
    }

    /**
     * @return array{name: string, zip: string, addr: string, tel: string, email: string}
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
