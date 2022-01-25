<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Library\CI_Session;

use function assert;

class CustomerInfoRepository
{
    /** @var CI_Session */
    private $session;

    public function __construct(CI_Session $session)
    {
        $this->session = $session;
    }

    public function save(CustomerInfoForm $data): void
    {
        $this->session->set_userdata('CustomerInfo', $data);
    }

    public function find(): CustomerInfoForm
    {
        $customerInfo = $this->session->userdata('CustomerInfo');
        assert($customerInfo instanceof CustomerInfoForm);

        return $customerInfo;
    }
}
