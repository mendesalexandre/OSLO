<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;

class LoginRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255'
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:255'
            ],
            'remember_me' => [
                'sometimes',
                'boolean'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'Email deve ter um formato válido.',
            'email.max' => 'O email não pode ter mais de 255 caracteres.',
            'password.required' => 'A senha é obrigatória.',
            'password.string' => 'A senha deve ser um texto válido.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
            'password.max' => 'A senha não pode ter mais de 255 caracteres.',
            'remember_me.boolean' => 'O campo "lembrar-me" deve ser verdadeiro ou falso.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'email' => 'email',
            'password' => 'senha',
            'remember_me' => 'lembrar-me',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalizar email para lowercase
        if ($this->has('email')) {
            $this->merge([
                'email' => strtolower(trim($this->email))
            ]);
        }

        // Garantir que remember_me seja boolean
        if ($this->has('remember_me')) {
            $this->merge([
                'remember_me' => filter_var($this->remember_me, FILTER_VALIDATE_BOOLEAN)
            ]);
        }
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        // Log da tentativa de login com dados inválidos
        Log::warning('Tentativa de login com dados inválidos', [
            'email' => $this->email,
            'errors' => $validator->errors()->toArray(),
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'timestamp' => now()
        ]);

        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $validator->errors()
            ], 422)
        );
    }

    /**
     * Get the validated email.
     */
    public function getEmail(): string
    {
        return $this->validated('email');
    }

    /**
     * Get the validated password.
     */
    public function getPassword(): string
    {
        return $this->validated('senha');
    }

    /**
     * Check if user wants to be remembered.
     */
    public function shouldRemember(): bool
    {
        return $this->validated('remember_me', false);
    }

    /**
     * Get token expiration time based on remember_me option.
     */
    public function getTokenExpirationHours(): int
    {
        return $this->shouldRemember() ? 24 * 7 : 24; // 7 dias se "lembrar", senão 24 horas
    }

    /**
     * Get the sanitized data for logging (without password).
     */
    public function getSafeData(): array
    {
        return [
            'email' => $this->getEmail(),
            'remember_me' => $this->shouldRemember(),
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'timestamp' => now()
        ];
    }

    /**
     * Add additional validation rules after the basic ones.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // Validação adicional para verificar se não é um email suspeito
            if ($this->has('email')) {
                $email = $this->getEmail();

                // Verificar se não é um email com muitos caracteres especiais (possível tentativa de injection)
                if (preg_match('/[<>"\']/', $email)) {
                    $validator->errors()->add('email', 'Email contém caracteres não permitidos.');
                }

                // Verificar domínios suspeitos (opcional - você pode configurar uma blacklist)
                $suspiciousDomains = config('security.suspicious_email_domains', []);
                $domain = substr(strrchr($email, "@"), 1);

                if (in_array($domain, $suspiciousDomains)) {
                    $validator->errors()->add('email', 'Domínio de email não permitido.');

                    Log::warning('Tentativa de login com domínio suspeito', [
                        'email' => $email,
                        'domain' => $domain,
                        'ip' => $this->ip()
                    ]);
                }
            }
        });
    }
}
