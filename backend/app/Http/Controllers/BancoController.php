<?php

namespace App\Http\Controllers;

use App\Models\Banco;
use App\Traits\RespostaApi;
use Illuminate\Http\JsonResponse;

class BancoController extends Controller
{
    use RespostaApi;

    public function index(): JsonResponse
    {
        $bancos = Banco::ativo()->orderBy('nome')->get();

        return $this->sucesso($bancos);
    }
}
