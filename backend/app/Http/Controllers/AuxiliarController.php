<?php

namespace App\Http\Controllers;

use App\Models\CapacidadeCivil;
use App\Models\EstadoCivil;
use App\Models\Nacionalidade;
use App\Models\PorteEmpresa;
use App\Models\Profissao;
use App\Models\RegimeBem;
use App\Models\TipoEmpresa;
use App\Traits\RespostaApi;
use Illuminate\Http\JsonResponse;

class AuxiliarController extends Controller
{
    use RespostaApi;

    private array $mapaModelos = [
        'estado-civil'    => EstadoCivil::class,
        'regime-bem'      => RegimeBem::class,
        'nacionalidade'   => Nacionalidade::class,
        'capacidade-civil' => CapacidadeCivil::class,
        'profissao'       => Profissao::class,
        'tipo-empresa'    => TipoEmpresa::class,
        'porte-empresa'   => PorteEmpresa::class,
    ];

    public function index(string $tabela): JsonResponse
    {
        if (!array_key_exists($tabela, $this->mapaModelos)) {
            return $this->naoEncontrado("Tabela auxiliar '{$tabela}' não encontrada");
        }

        $model = $this->mapaModelos[$tabela];
        $registros = $model::ativo()->orderBy('id')->get();

        return $this->sucesso($registros);
    }
}
