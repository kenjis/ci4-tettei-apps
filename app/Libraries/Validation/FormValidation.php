<?php

declare(strict_types=1);

namespace App\Libraries\Validation;

use App\Libraries\FormData;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\Validation\ValidationInterface;

use function assert;
use function is_array;

/**
 * FormData のバリデーション
 */
class FormValidation
{
    /** @var ValidationInterface */
    private $validation;

    public function __construct(ValidationInterface $validation)
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
        $post = $request->getPost();
        assert(is_array($post));

        $isValid = $this->validation->setRules($rules)->run($post);

        if (! $isValid) {
            return false;
        }

        $form->setData($post);

        return true;
    }
}
