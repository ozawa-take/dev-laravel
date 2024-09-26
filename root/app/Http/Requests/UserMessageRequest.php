<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserMessageRequest extends FormRequest
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
            'text' => 'required|max:255',
            'admin_id' => 'required|integer',
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
            'admin_id' => '管理者ID',
            'text' => '本文',
            'sendType' => '送信タイプ',

        ];
    }
}
