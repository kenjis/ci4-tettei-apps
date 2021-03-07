<?php

declare(strict_types=1);

namespace App\Libraries\Validation;

use Kenjis\CI3Compatible\Core\CI_Controller;
use Kenjis\CI3Compatible\Exception\RuntimeException;
use Kenjis\CI3Compatible\Library\CI_Form_validation;

class FieldValidation
{
    /** @var CI_Controller */
    private $CI;

    /** @var CI_Form_validation */
    private $form_validation;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->library('form_validation');
        // @phpstan-ignore-next-line
        $this->form_validation = $this->CI->form_validation;
    }

    /**
     * @param mixed                 $value
     * @param mixed                 $rules
     * @param array<string, string> $errors
     */
    public function validate($value, $rules, array $errors = []): bool
    {
        $this->form_validation->reset_validation();

        $data = ['field' => $value];
        $this->form_validation->set_data($data);

        $this->form_validation->set_rules('field', '', $rules, $errors);

        if ($this->form_validation->run() !== false) {
            return true;
        }

        throw new RuntimeException('不正な入力です。');
    }
}
