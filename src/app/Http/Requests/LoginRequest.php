<?php

namespace App\Http\Requests;

//use Illuminate\Foundation\Http\FormRequest;

use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
//use App\Http\Requests\LoginRequest as CustomLoginRequest;

class LoginRequest extends FortifyLoginRequest
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
            'email' => ['required', 'email','exists:users,email'],
            'password' => ['required'],
        ];
    }

    public function messages()
    {
        return[
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => 'メールアドレスはメール形式で入力してください',
            'email.exists' => 'ログイン情報が登録されていません',
            'password.required' => 'パスワードを入力してください',
            'password.exists' => 'パスワードが間違っています',
        ];
    }

}
