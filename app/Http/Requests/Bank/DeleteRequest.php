<?php

namespace App\Http\Requests\Bank;

use Illuminate\Foundation\Http\FormRequest;

class DeleteRequest extends FormRequest
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
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O ID do banco é obrigatório.',
            'id.string' => 'O ID do banco é inválido.',
            'id.exists' => 'O ID do banco informado não existe.',
        ];
    }
}
