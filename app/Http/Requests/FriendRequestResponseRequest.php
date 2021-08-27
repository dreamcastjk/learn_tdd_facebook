<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Exceptions\ValidationErrorException;
use Illuminate\Contracts\Validation\Validator;

class FriendRequestResponseRequest extends FormRequest
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
            'user_id' => 'required',
            'status' => 'required',
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
