<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\ValidationErrorException;
use Illuminate\Contracts\Validation\Validator;

class IgnoreFriendRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required',
        ];
    }

    /**
     * @throws ValidationErrorException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationErrorException(json_encode($validator->errors()));
    }
}
