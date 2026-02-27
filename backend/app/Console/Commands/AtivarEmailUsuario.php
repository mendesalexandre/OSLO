<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AtivarEmailUsuario extends Command
{
    protected $signature = 'usuario:ativar-email {email?}';
    protected $description = 'Ativa o email de um usuário (usado para testes)';

    public function handle()
    {
        $email = $this->argument('email');

        if (!$email) {
            // Ativar último usuário criado
            $usuario = User::latest('data_cadastro')->first();

            if (!$usuario) {
                $this->error('Nenhum usuário encontrado.');
                return 1;
            }

            $email = $usuario->email;
        } else {
            $usuario = User::where('email', $email)->first();

            if (!$usuario) {
                $this->error("Usuário com email '{$email}' não encontrado.");
                return 1;
            }
        }

        if ($usuario->email_verificado_em) {
            $this->warn("Email '{$email}' já está verificado.");
            return 0;
        }

        $usuario->email_verificado_em = now();
        $usuario->token_verificacao = null;
        $usuario->save();

        $this->info("✅ Email '{$email}' verificado com sucesso!");
        $this->info("Empresa: {$usuario->empresa->razao_social}");
        $this->info("Trial até: {$usuario->empresa->trial_fim->format('d/m/Y')}");

        return 0;
    }
}
