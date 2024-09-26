<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        // データ更新時、自身の重複を除外するためignoreを設定
        return [
            'username'     => [
                'required',
                'max:255',
                Rule::unique('users')->ignore($this->user->id),
            ],
            'mail_address' => 'required|max:255',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'username'     => 'ユーザーID',
            'mail_address' => 'メールアドレス',
        ];
    }
}
