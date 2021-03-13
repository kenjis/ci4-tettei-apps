<?php

declare(strict_types=1);

namespace App\Models\Shop;

use Kenjis\CI3Compatible\Library\CI_Session;
use Kenjis\CI3Compatible\Test\TestCase\UnitTestCase;
use Kenjis\CI3Compatible\Test\Traits\SessionTest;

class CustomerInfoRepositoryTest extends UnitTestCase
{
    use SessionTest;

    /** @var CustomerInfoRepository */
    private $customerInfoRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->customerInfoRepository = new CustomerInfoRepository(new CI_Session());
    }

    public function test_set_and_get(): void
    {
        $expected = [
            'name'  => '名前',
            'zip'   => '111-1111',
            'addr'  => '東京都千代田区',
            'tel'   => '03-3333-3333',
            'email' => 'foo@example.jp',
        ];
        $form = new CustomerInfoForm();
        $form->getValidationRules();
        $form->setData($expected);

        $this->customerInfoRepository->save($form);

        $actual = $this->customerInfoRepository->find();

        $this->assertEquals($expected['name'], $actual['name']);
        $this->assertEquals($expected['email'], $actual['email']);
    }
}
