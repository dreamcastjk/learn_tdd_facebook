<?php

namespace App\Http\Requests;

use JetBrains\PhpStorm\ArrayShape;
use Illuminate\Foundation\Http\FormRequest;

class PostStoreRequest extends FormRequest
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
    #[ArrayShape(['data.attributes.body' => "string"])]
    public function rules(): array
    {
        return [
            'data.attributes.body' => ''
        ];
    }
}
