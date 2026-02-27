<?php

namespace App\Http\Resources\Doi;

use App\Services\ConsultaCEPService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $json = [];

        // Tipo Declaração - sempre 0 se não informado
        // CASO TIPO DECLARAÇÃO FOR 1 ENTÃO MANDAR COMO ZERO
        if ($this->debug['tipoDeclaracao'] == 1) {
            $json['tipoDeclaracao'] = '0';
        } else {
            $json['tipoDeclaracao'] = (string) $this->debug['tipoDeclaracao'] ?? '0';
        }
        // $json['tipoDeclaracao'] = (string) $this->debug['tipoDeclaracao'] ?? '0';

        // Tipo Serviço
        $json['tipoServico'] = (string) $this->debug['tipoServico'];

        // Data Lavratura
        $json['dataLavraturaRegistroAverbacao'] = $this->debug['dataLavraturaRegistroAverbacao'];

        // Tipo Ato - ATENÇÃO: valor 4 não é permitido, usando 1 como padrão
        $json['tipoAto'] = !empty($this->debug['tipoAto']) ? (string) $this->debug['tipoAto'] : '1';

        // Tipo Livro - padrão 1
        $json['tipoLivro'] = !empty($this->debug['tipoLivro']) ? (string) $this->debug['tipoLivro'] : '1';

        // Número Livro
        $json['numeroLivro'] = (string) $this->debug['numeroLivro'];

        // Folha
        $json['folha'] = (string) $this->debug['folha'];

        // Matrícula - remove caracteres não numéricos
        $json['matricula'] = (string) preg_replace('/[^0-9]/', '', $this->debug['matricula']);

        // Código IBGE - deve ter 7 dígitos numéricos
        // if (!empty($this->debug['codigoIbge'])) {
        //     $codigoIbge = preg_replace('/[^0-9]/', '', $this->debug['codigoIbge']);
        //     $json['codigoIbge'] = str_pad($codigoIbge, 7, '0', STR_PAD_LEFT);
        // } else {
        //     $json['codigoIbge'] = '5103254'; // Código padrão
        // }
        // Código IBGE - tenta buscar do CEP se não tiver
        if (!empty($this->debug['codigoIbge'])) {
            $codigoIbge = preg_replace('/[^0-9]/', '', $this->debug['codigoIbge']);
            $json['codigoIbge'] = str_pad($codigoIbge, 7, '0', STR_PAD_LEFT);
        } elseif (!empty($this->debug['cep'])) {
            // Tenta buscar do ViaCEP
            $consultaCEP = app(ConsultaCEPService::class);
            $codigoIbge = $consultaCEP->obterCodigoIBGE($this->debug['cep']);
            $json['codigoIbge'] = $codigoIbge ?? '5103254'; // Código padrão se falhar
        } else {
            $json['codigoIbge'] = '5103254'; // Código padrão
        }


        // Número Registro Averbação
        if ($this->debug['numeroRegistroAverbacao'] !== null) {
            $json['numeroRegistroAverbacao'] = (string) $this->debug['numeroRegistroAverbacao'];
        }

        // Natureza Título
        $json['naturezaTitulo'] = (string) $this->debug['naturezaTitulo'];

        // Existe DOI Anterior
        $json['existeDoiAnterior'] = $this->debug['existeDoiAnterior'] ?? false;

        // Data Negócio Jurídico
        if ($this->debug['dataNegocioJuridico'] !== null) {
            $json['dataNegocioJuridico'] = $this->debug['dataNegocioJuridico'];
        }

        // Tipo Operação Imobiliária
        // $json['tipoOperacaoImobiliaria'] = (string) $this->debug['tipoOperacaoImobiliaria'];
        // SE A OPERAÇÃO FOR 53 ENTÃO FORÇAR O 67
        // se for o 49 então forçar o 70

        // $json['tipoOperacaoImobiliaria'] = ($this->debug['tipoOperacaoImobiliaria'] == 53) ? '67' : (string) $this->debug['tipoOperacaoImobiliaria'];
        if ($this->debug['tipoOperacaoImobiliaria'] == 53) {
            $json['tipoOperacaoImobiliaria'] = '67';
        } elseif ($this->debug['tipoOperacaoImobiliaria'] == 49) {
            $json['tipoOperacaoImobiliaria'] = '70';
        } else {
            $json['tipoOperacaoImobiliaria'] = (string) $this->debug['tipoOperacaoImobiliaria'];
        }

        // Se tipo 39, informar descrição
        if ($this->debug['tipoOperacaoImobiliaria'] == 39) {
            $json['descricaoOutrasOperacoesImobiliarias'] = (string) ($this->debug['descricaoOutrasOperacoesImobiliarias'] ?? '');
        }

        // Valor Operação Imobiliária
        if ($this->debug['valorOperacaoImobiliaria'] !== null) {
            $json['valorOperacaoImobiliaria'] = $this->debug['valorOperacaoImobiliaria'];
        }

        // Indicador Não Consta Valor Operação
        $json['indicadorNaoConstaValorOperacaoImobiliaria'] = $this->debug['indicadorNaoConstaValorOperacaoImobiliaria'] ?? false;

        // Valor Base Cálculo ITBI/ITCMD
        if ($this->debug['valorBaseCalculoItbiItcmd'] !== null) {
            $json['valorBaseCalculoItbiItcmd'] = $this->debug['valorBaseCalculoItbiItcmd'];
        }

        // Indicador Não Consta Valor Base Cálculo
        $json['indicadorNaoConstaValorBaseCalculoItbiItcmd'] = $this->debug['indicadorNaoConstaValorBaseCalculoItbiItcmd'] ?? false;

        // Forma Pagamento
        $json['formaPagamento'] = $this->debug['formaPagamento'] ? (string) $this->debug['formaPagamento'] : '11';

        // ⚠️ CAMPOS OBRIGATÓRIOS - sempre enviar com false como padrão
        $json['indicadorPermutaBens'] = $this->debug['indicadorPermutaBens'] ?? false;
        $json['indicadorPagamentoDinheiro'] = $this->debug['indicadorPagamentoDinheiro'] ?? false;

        // Valor Parte Transacionada
        if (empty($this->debug['valorParteTransacionada']) || is_null($this->debug['valorParteTransacionada']) || $this->debug['valorParteTransacionada'] === 0) {
            $json['valorParteTransacionada'] = 100;
        } else {
            $json['valorParteTransacionada'] = $this->debug['valorParteTransacionada'];
        }

        // Tipo Parte Transacionada
        $json['tipoParteTransacionada'] = empty($this->debug['tipoParteTransacionada']) ? "1" : (string) $this->debug['tipoParteTransacionada'];

        // CIB
        if ($this->debug['cib'] !== null) {
            $json['cib'] = $this->debug['cib'];
        }

        // Destinação
        $json['destinacao'] = (string) $this->debug['destinacao'];

        // ⚠️ CAMPO OBRIGATÓRIO - sempre enviar com false como padrão
        $json['indicadorImovelPublicoUniao'] = $this->debug['indicadorImovelPublicoUniao'] ?? false;

        // Área Imóvel
        if ($this->debug['areaImovel'] !== null) {
            $json['areaImovel'] = $this->debug['areaImovel'];
        }

        // Indicador Área Lote Não Consta
        $json['indicadorAreaLoteNaoConsta'] = $this->debug['indicadorAreaLoteNaoConsta'] ?? false;

        // Área Construída e Indicador
        if ($this->debug['areaConstruida'] !== null) {
            $json['areaConstruida'] = $this->debug['areaConstruida'];
            $json['indicadorAreaConstruidaNaoConsta'] = false;
        } else {
            // Se não tiver área construída, marca como "não consta"
            $json['indicadorAreaConstruidaNaoConsta'] = true;
        }

        // Tipo Imóvel
        if ($this->debug['tipoImovel'] !== null) {
            $json['tipoImovel'] = (string) $this->debug['tipoImovel'];
        }

        // Tipo Logradouro - obrigatório para destinação Urbano
        $json['tipoLogradouro'] = $this->debug['tipoLogradouro'] ?? 'Não informado';

        // Nome Logradouro
        // if ($this->debug['nomeLogradouro'] !== null) {
        //     $json['nomeLogradouro'] = $this->debug['nomeLogradouro'] ;
        // }

        $json['nomeLogradouro'] = $this->debug['nomeLogradouro'] ?? 'Não informado';

        // Número Imóvel
        // if ($this->debug['numeroImovel'] !== null) {
        //     $json['numeroImovel'] = (string) $this->debug['numeroImovel'];
        // }

        // if ($this->debug['numeroImovel'] !== null) {
        $json['numeroImovel'] = (string) $this->debug['numeroImovel'] ?? '0';
        // }

        // Complemento Endereço
        if ($this->debug['complementoEndereco'] !== null) {
            $json['complementoEndereco'] = $this->debug['complementoEndereco'];
        }

        // Bairro - obrigatório para destinação Urbano
        $json['bairro'] = $this->debug['bairro'] ?? 'Não informado';

        // CEP
        // if ($this->debug['cep'] !== null) {
        //     $json['cep'] = $this->debug['cep'] ?? '78520000';
        // }
        $json['cep'] = $this->debug['cep'] ?? '78520000';

        // Localização
        if ($this->debug['localizacao'] !== null) {
            $json['localizacao'] = $this->debug['localizacao'] ?? 'Não informado';
        }

        // Código Nacional Matrícula (caso não tenha matrícula comum)
        if ($this->debug['codigoNacionalMatricula'] !== null) {
            $json['codigoNacionalMatricula'] = $this->debug['codigoNacionalMatricula'] ?? 'Não informado';
        }

        // Denominação - obrigatório para destinação Rural
        $json['denominacao'] = $this->debug['denominacao'] ?? 'Não informado';

        // ============================================
        // ALIENANTES (Transmitentes)
        // ============================================
        if ($this->relationLoaded('transmitentes') && $this->transmitentes->count() > 0) {
            $alienantes = [];

            foreach ($this->transmitentes as $transmitente) {
                $arrayAlienantes = $transmitente->data;

                if (is_array($arrayAlienantes)) {
                    foreach ($arrayAlienantes as $dadosTransmitente) {
                        $transmitenteData = [];

                        // ⚠️ CAMPOS OBRIGATÓRIOS - sempre enviar com false como padrão
                        $transmitenteData['indicadorRepresentante'] = (bool) ($dadosTransmitente['indicadorRepresentante'] ?? false);
                        $transmitenteData['indicadorEspolio'] = (bool) ($dadosTransmitente['indicadorEspolio'] ?? false);
                        $transmitenteData['indicadorEstrangeiro'] = (bool) ($dadosTransmitente['indicadorEstrangeiro'] ?? false);
                        $transmitenteData['indicadorNaoConstaParticipacaoOperacao'] = (bool) ($dadosTransmitente['indicadorNaoConstaParticipacaoOperacao'] ?? false);

                        // Indicador NI Identificado
                        if (isset($dadosTransmitente['indicadorNiIdentificado']) && $dadosTransmitente['indicadorNiIdentificado'] === false) {
                            $transmitenteData['indicadorNiIdentificado'] = false;
                            $transmitenteData['motivoNaoIdentificacaoNi'] = (string) ($dadosTransmitente['motivoNaoIdentificacaoNi'] ?? '');
                        } else {
                            $transmitenteData['indicadorNiIdentificado'] = true;
                        }

                        // NI
                        if (isset($dadosTransmitente['ni']) && $dadosTransmitente['ni'] !== null) {
                            $transmitenteData['ni'] = $dadosTransmitente['ni'];
                        }

                        // Participação
                        if (isset($dadosTransmitente['participacao']) && $dadosTransmitente['participacao'] !== null) {
                            $transmitenteData['participacao'] = $dadosTransmitente['participacao'];
                        }

                        // Representantes
                        if (isset($dadosTransmitente['representantes']) && is_array($dadosTransmitente['representantes']) && !empty($dadosTransmitente['representantes'])) {
                            $transmitenteData['representantes'] = [];
                            foreach ($dadosTransmitente['representantes'] as $representante) {
                                if (isset($representante['ni']) && $representante['ni'] !== null) {
                                    $transmitenteData['representantes'][] = ['ni' => $representante['ni']];
                                }
                            }
                        }

                        $alienantes[] = $transmitenteData;
                    }
                }
            }

            if (empty($alienantes)) {
                throw new \Exception('É obrigatório informar ao menos um vendedor/transmitente');
            }
            $json['alienantes'] = $alienantes;
        } else {
            throw new \Exception('É obrigatório informar ao menos um vendedor/transmitente');
        }

        // ============================================
        // ADQUIRENTES
        // ============================================
        if ($this->relationLoaded('adquirentes') && $this->adquirentes->count() > 0) {
            $adquirentes = [];

            foreach ($this->adquirentes as $adquirente) {
                $arrayAdquirentes = $adquirente->data;

                if (is_array($arrayAdquirentes)) {
                    foreach ($arrayAdquirentes as $dadosAdquirente) {
                        $adquirenteData = [];

                        // ⚠️ CAMPOS OBRIGATÓRIOS - sempre enviar com false como padrão
                        $adquirenteData['indicadorRepresentante'] = (bool) ($dadosAdquirente['indicadorRepresentante'] ?? false);
                        $adquirenteData['indicadorEspolio'] = (bool) ($dadosAdquirente['indicadorEspolio'] ?? false);
                        $adquirenteData['indicadorEstrangeiro'] = (bool) ($dadosAdquirente['indicadorEstrangeiro'] ?? false);
                        $adquirenteData['indicadorNaoConstaParticipacaoOperacao'] = (bool) ($dadosAdquirente['indicadorNaoConstaParticipacaoOperacao'] ?? false);

                        // Indicador NI Identificado
                        if (isset($dadosAdquirente['indicadorNiIdentificado']) && $dadosAdquirente['indicadorNiIdentificado'] === false) {
                            $adquirenteData['indicadorNiIdentificado'] = false;
                            $adquirenteData['motivoNaoIdentificacaoNi'] = (string) ($dadosAdquirente['motivoNaoIdentificacaoNi'] ?? '');
                        } else {
                            $adquirenteData['indicadorNiIdentificado'] = true;
                        }

                        // NI
                        if (isset($dadosAdquirente['ni']) && $dadosAdquirente['ni'] !== null) {
                            $adquirenteData['ni'] = $dadosAdquirente['ni'];
                        }

                        // Participação
                        if (isset($dadosAdquirente['participacao']) && $dadosAdquirente['participacao'] !== null) {
                            $adquirenteData['participacao'] = $dadosAdquirente['participacao'];
                        }

                        // Representantes
                        if (isset($dadosAdquirente['representantes']) && is_array($dadosAdquirente['representantes']) && !empty($dadosAdquirente['representantes'])) {
                            $adquirenteData['representantes'] = [];
                            foreach ($dadosAdquirente['representantes'] as $representante) {
                                if (isset($representante['ni']) && $representante['ni'] !== null) {
                                    $adquirenteData['representantes'][] = ['ni' => $representante['ni']];
                                }
                            }
                        }

                        $adquirentes[] = $adquirenteData;
                    }
                }
            }

            if (empty($adquirentes)) {
                throw new \Exception('É obrigatório informar ao menos um comprador/adquirente');
            }
            $json['adquirentes'] = $adquirentes;
        } else {
            throw new \Exception('É obrigatório informar ao menos um comprador/adquirente');
        }

        return $json;
    }
}
