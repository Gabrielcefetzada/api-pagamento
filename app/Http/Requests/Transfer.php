<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Transfer extends FormRequest
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
            'value' => 'required|decimal:1,2|min:0.01',
            'payee' => 'required|integer',
            'payer' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'required'      => 'O campo :attribute é obrigatório',
            'integer'       => 'O campo :attribute deve ser um inteiro',
            'value.min'     => 'O campo :attribute deve ser maior que 0',
            'value.decimal' => 'O campo :attribute deve ser um decimal com no máximo duas casas decimais e no mínimo uma',
        ];
    }
}
