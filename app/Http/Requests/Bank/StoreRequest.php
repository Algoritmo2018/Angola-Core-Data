<?php

namespace App\Http\Requests\Bank;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'bank_name' => [
                'required',
                'string',
                'unique:banks,bank_name',
            ],
            'short_name' => [
                'required',
                'string',
                'unique:banks,short_name',
            ],
            'country_prefix' => [
                'required',
                'string',
            ],
            'bank_prefix' => [
                'required',
                'string',
                'min:4',
                'max:4',
                'unique:banks,bank_prefix',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'bank_name.required' => 'O nome do banco é obrigatório.',
            'bank_name.string' => 'O nome do banco deve ser texto.',
            'bank_name.unique' => 'O nome do banco já está cadastrado.',

            'short_name.required' => 'O nome abreviado do banco é obrigatório.',
            'short_name.string' => 'O nome abreviado do banco deve ser texto.',
            'short_name.unique' => 'O nome abreviado do banco já está cadastrado.',

            'country_prefix.required' => 'O código do banco é obrigatório.',
            'country_prefix.string' => 'O código do banco deve ser texto.',

            'bank_prefix.required' => 'O prefixo do banco é obrigatório.',
            'bank_prefix.string' => 'O prefixo do banco deve ser texto.',
            'bank_prefix.min' => 'O prefixo do banco deve ter 4 dígitos.',
            'bank_prefix.max' => 'O prefixo do banco deve ter 4 dígitos.',
            'bank_prefix.unique' => 'O prefixo do banco já está cadastrado.',
        ];
    }
}
