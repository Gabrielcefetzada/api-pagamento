<?php

namespace App\Http\Requests;

class UserRegisterStoreKeeper extends UserRegister
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

        return array_merge([
            'cnpj' => 'required|min:14|regex:/\d{2}\.?\d{3}\.?\d{3}\/?\d{4}-?\d{2}/'
        ], $this->commonRules);
    }

    public function messages(): array
    {
        return array_merge([
            'cnpj.required' => 'O campo CNPJ é obrigatório para registrar Lojista',
            'cnpj.regex'    => 'CNPJ inválido'
        ], $this->commonMessages);
    }
}
