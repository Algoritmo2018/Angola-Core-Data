<?php

namespace App\Http\Requests\Municipality;

use App\Models\Province;
use Illuminate\Foundation\Http\FormRequest;

class StoreMunicipalityRequest extends FormRequest
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
                'unique:municipalities',
            ],
            'province_id' => [
                'required',
                'string', 
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do município é obrigatório.',
            'name.string' => 'O nome do município deve ser texto.',
            'name.min' => 'O nome do município deve ter pelo menos :min caracteres.',
            'name.max' => 'O nome do município não pode ter mais de :max caracteres.',
            'name.unique' => 'Já existe um município com este nome.',

            'province_id.required' => 'O campo province_id é obrigatório.',
            'province_id.string' => 'O campo province_id deve ser do tipo string.', 
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Verificando se a província selecionada existe
            $provinceExists = Province::where('id', $this->province_id)->exists();

            if (!$provinceExists) {
                $validator->errors()->add('province_id', 'A província selecionada não existe.');
            }
        });
    }
}
