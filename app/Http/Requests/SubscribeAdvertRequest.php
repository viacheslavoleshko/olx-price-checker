<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscribeAdvertRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'url' => [
                'required',
                'active_url',
                'regex:/^https:\/\/(www\.)?olx\.ua\/d\/(uk\/)?obyavlenie\/[\w\-]+-(ID[\w]+)\.html(\?.*)?$/',
            ],
        ];
    }
}
