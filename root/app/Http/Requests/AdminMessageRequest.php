<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminMessageRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title'   => 'required|max:255',
            'user_id' => 'required|integer',
            'text' => 'required|max:255',
            'sendType' => 'required|integer',
        ];
    }


    /**
     * Get custom attribute names for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes()
    {
        return [
            'title'              => '件名',
            'user_id' => 'ユーザーID',
            'text' => '本文',
            'sendType' => '送信タイプ',
        ];
    }
}
