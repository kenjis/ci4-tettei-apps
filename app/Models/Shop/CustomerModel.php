<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Library\CI_Session;

class CustomerModel
{
    /** @var CI_Session */
    private $session;

    public function __construct(CI_Session $session)
    {
        $this->session = $session;
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
