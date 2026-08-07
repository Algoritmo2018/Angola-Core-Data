<?php

namespace App\Http\Requests\Bank;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => [
                'required',
                'string',
                'exists:banks,id',
            ],
            'bank_name' => [
                'string',
                Rule::unique('banks')->ignore($this->route('id')),
            ],
            'short_name' => [
                'string',
                Rule::unique('banks')->ignore($this->route('id')),
            ],
            'country_prefix' => [
                'string',
            ],
            'bank_prefix' => [
                'string',
                'min:4',
                'max:4',
                Rule::unique('banks')->ignore($this->route('id')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O ID do banco é obrigatório.',
            'id.string' => 'O ID do banco é inválido.',
            'id.exists' => 'O ID do banco informado não existe.',

            'bank_name.string' => 'O nome do banco deve ser texto.',
            'bank_name.unique' => 'O nome do banco já está cadastrado.',

            'short_name.string' => 'O nome abreviado do banco deve ser texto.',
            'short_name.unique' => 'O nome abreviado do banco já está cadastrado.',

            'country_prefix.string' => 'O código do banco deve ser texto.',

            'bank_prefix.string' => 'O prefixo do banco deve ser texto.',
            'bank_prefix.min' => 'O prefixo do banco deve ter 4 dígitos.',
            'bank_prefix.max' => 'O prefixo do banco deve ter 4 dígitos.',
            'bank_prefix.unique' => 'O prefixo do banco já está cadastrado.',
        ];
    }
}
