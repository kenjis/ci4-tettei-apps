<?php

declare(strict_types=1);

namespace App\Libraries\Validation;

use Kenjis\CI3Compatible\Core\CI_Controller;
use Kenjis\CI3Compatible\Library\CI_Form_validation;

abstract class CustomValidation
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

        $this->form_validation->reset_validation();
        $this->set_validation_rules();
    }

    /**
     * バリデーションの設定
     */
    abstract protected function set_validation_rules(): void;

    /**
     * @param mixed                 $field
     * @param mixed                 $rules
     * @param array<string, string> $errors
     */
    protected function set_rules(
        $field,
        string $label = '',
        $rules = [],
        array $errors = []
    ): void {
        $this->form_validation->set_rules($field, $label, $rules, $errors);
    }

    /**
     * @param array<string, string> $data
     */
    public function validate(array $data = []): bool
    {
        if ($data !== []) {
            $this->form_validation->set_data($data);
            $this->set_validation_rules();
        }

        return $this->run();
    }

    public function run(): bool
    {
        return $this->form_validation->run();
    }

    /**
     * @return array<string, string>
     */
    public function error_array(): array
    {
        return $this->form_validation->error_array();
    }
}
