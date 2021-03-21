<?php

declare(strict_types=1);

namespace App\Libraries\Validation;

use App\Libraries\FormData;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\Validation\Validation;

/**
 * FormData のバリデーション
 */
class FormValidation
{
    /** @var Validation */
    private $validation;

    public function __construct(Validation $validation)
    {
        $this->validation = $validation;
        $this->validation->reset();
    }

    /**
     * リクエストデータを検証し、OKならFormDataにセットする
     *
     * @param string $group バリデーションルールのグループ
     */
    public function validate(
        IncomingRequest $request,
        FormData $form,
        string $group = 'common'
    ): bool {
        $rules = $form->getValidationRules($group);
        $isValid = $this->validation->setRules($rules)->run($request->getPost());

        if (! $isValid) {
            return false;
        }

        $form->setData($request->getPost());

        return true;
    }
}
