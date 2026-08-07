<?php

namespace App\Http\Requests\Comune;

use App\Models\Municipality;
use Illuminate\Foundation\Http\FormRequest;

class StoreComuneRequest extends FormRequest
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
                'unique:comunes',
            ],
            'municipality_id' => [
                'required',
                'string', 
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do comune é obrigatório.',
            'name.string' => 'O nome do comune deve ser texto.',
            'name.min' => 'O nome do comune deve ter pelo menos :min caracteres.',
            'name.max' => 'O nome do comune não pode ter mais de :max caracteres.',
            'name.unique' => 'Já existe um comune com este nome.',

            'municipality_id.required' => 'O campo municipality_id é obrigatório.',
            'municipality_id.string' => 'O campo municipality_id deve ser do tipo string.', 
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Verificando se o município selecionado existe
            $municipalityExists = Municipality::where('id', $this->municipality_id)->exists();

            if (!$municipalityExists) {
                $validator->errors()->add('municipality_id', 'O município selecionado não existe.');
            }
        });
    }
}
