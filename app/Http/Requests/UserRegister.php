<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegister extends FormRequest
{
    protected array $commonRules = [
        'email'    => 'required|email',
        'password' => "required|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/|min:6",
        'cpf'      => 'required|regex:/^\d{3}.?\d{3}.?\d{3}\-?\d{2}$/',
        'name'     => 'required|regex:/^\S+\s+\S+/'
    ];

    protected array $commonMessages = [
        'password.regex' => 'A senha deve cumprir os requisitos: Ao menos uma letra minúscula, uma maiúscula, um número e um caracter especial',
        'password.min'   => 'A senha deve ter pelo menos seis caracteres',
        'required'       => 'O campo :attribute é obrigatório',
        'cpf.regex'      => 'O cpf é inválido',
        'name.regex'     => 'Por favor, insira um nome completo'
    ];
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
        return $this->commonRules;
    }

    public function messages(): array
    {
        return $this->commonMessages;
    }
}
