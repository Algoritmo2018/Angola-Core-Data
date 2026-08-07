<?php

namespace App\Http\Requests\Province;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProvinceRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'min:1',
                'max:255',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'name.string'   => 'O campo nome deve ser texto.',
            'name.min'      => 'O campo nome deve ter pelo menos :min caracteres.',
            'name.max'      => 'O campo nome não pode ter mais de :max caracteres.',
            'name.unique'   => 'Já existe uma província com este nome.',
        ];
    }
}
