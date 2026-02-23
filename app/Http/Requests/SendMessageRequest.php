<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
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
            'to' => ['required', 'string', 'exists:players,pseudo'],
            'content' => ['required', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'to.required' => 'Le destinataire est requis.',
            'to.exists' => 'Joueur introuvable.',
            'content.required' => 'Le contenu est requis.',
            'content.max' => 'Le contenu ne peut pas dépasser 500 caractères.',
        ];
    }
}
