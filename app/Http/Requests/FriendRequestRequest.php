<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\ValidationErrorException;
use Illuminate\Contracts\Validation\Validator;

class FriendRequestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'friend_id' => 'required'
        ];
    }

    /**
     * @param Validator $validator
     *
     * @throws ValidationErrorException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationErrorException(json_encode($validator->errors()));
    }
}
