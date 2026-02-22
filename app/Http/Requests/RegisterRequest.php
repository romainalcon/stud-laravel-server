<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pseudo' => ['required', 'string', 'max:30', 'unique:players,pseudo'],
            'port' => ['required', 'integer', 'between:8000,8099'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'pseudo.unique' => 'Ce pseudo est déjà pris.',
            'pseudo.required' => 'Le pseudo est requis.',
            'pseudo.max' => 'Le pseudo ne peut pas dépasser 30 caractères.',
            'port.required' => 'Le port est requis.',
            'port.integer' => 'Le port doit être un entier.',
            'port.between' => 'Le port doit être entre 8000 et 8099.',
        ];
    }
}
