import axios from 'axios'

/**
 * Service para consulta de dados de CNPJ usando a BrasilAPI.
 * API pública e gratuita: https://brasilapi.com.br
 */
class CnpjService {
  /**
   * Valida se o CNPJ tem formato válido (14 dígitos)
   */
  static validarCnpj(cnpj) {
    const cnpjLimpo = cnpj?.replace(/\D/g, '') || ''
    return cnpjLimpo.length === 14
  }

  /**
   * Remove formatação do CNPJ (pontos, barras e hífens)
   */
  static limparCnpj(cnpj) {
    return cnpj?.replace(/\D/g, '') || ''
  }

  /**
   * Formata o CNPJ no padrão XX.XXX.XXX/XXXX-XX
   */
  static formatarCnpj(cnpj) {
    const cnpjLimpo = this.limparCnpj(cnpj)

    if (cnpjLimpo.length !== 14) {
      return cnpj
    }

    return cnpjLimpo.replace(
      /^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/,
      '$1.$2.$3/$4-$5'
    )
  }

  /**
   * Busca dados do CNPJ na BrasilAPI
   * @param {string} cnpj - CNPJ com ou sem formatação
   * @returns {Promise<Object>} Dados da empresa
   */
  static async buscarDados(cnpj) {
    const cnpjLimpo = this.limparCnpj(cnpj)

    if (!this.validarCnpj(cnpj)) {
      throw new Error('CNPJ inválido. Digite um CNPJ válido com 14 dígitos.')
    }

    try {
      const response = await axios.get(
        `https://brasilapi.com.br/api/cnpj/v1/${cnpjLimpo}`,
        {
          timeout: 10000 // 10 segundos
        }
      )

      return this.formatarResposta(response.data)
    } catch (error) {
      if (error.response?.status === 404) {
        throw new Error('CNPJ não encontrado na Receita Federal')
      } else if (error.response?.status === 429) {
        throw new Error('Muitas requisições. Aguarde alguns segundos e tente novamente.')
      } else if (error.code === 'ECONNABORTED') {
        throw new Error('Tempo de espera excedido. Tente novamente.')
      } else {
        throw new Error('Erro ao buscar dados do CNPJ. Verifique sua conexão.')
      }
    }
  }

  /**
   * Formata a resposta da API para o formato usado no sistema
   */
  static formatarResposta(dados) {
    return {
      cnpj: dados.cnpj,
      razao_social: dados.razao_social || '',
      nome_fantasia: dados.nome_fantasia || '',
      email: dados.email || '',
      telefone: this.formatarTelefone(dados.ddd_telefone_1),
      estado: dados.uf || '',
      cidade: dados.municipio || '',
      endereco: this.formatarEndereco(dados),
      situacao: dados.descricao_situacao_cadastral || '',
      data_abertura: dados.data_inicio_atividade || '',
      natureza_juridica: dados.natureza_juridica || '',
      porte: dados.porte || '',
      // Dados completos para referência
      dados_completos: dados
    }
  }

  /**
   * Formata o telefone removendo caracteres especiais
   */
  static formatarTelefone(dddTelefone) {
    if (!dddTelefone) return ''

    const telefoneLimpo = dddTelefone.replace(/\D/g, '')

    if (telefoneLimpo.length < 10) return ''

    return telefoneLimpo
  }

  /**
   * Formata o endereço completo
   */
  static formatarEndereco(dados) {
    const partes = []

    if (dados.logradouro) {
      let endereco = dados.logradouro
      if (dados.numero) endereco += `, ${dados.numero}`
      if (dados.complemento) endereco += ` - ${dados.complemento}`
      partes.push(endereco)
    }

    if (dados.bairro) partes.push(dados.bairro)
    if (dados.municipio && dados.uf) partes.push(`${dados.municipio}/${dados.uf}`)
    if (dados.cep) partes.push(`CEP: ${dados.cep}`)

    return partes.join(' - ')
  }
}

export default CnpjService
