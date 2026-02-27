export const setTitulo = (titulo = "Nome da Página") => {
  document.title = `${titulo.replace(" - Sistema Oslo", "")} - Sistema Oslo`;
};

export default async function getCep(cep, comercial) {
  comercial = comercial ? "Comercial" : "";

  if (!cep) {
    return {};
  }

  const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`)
    .then((response) => response.json())
    .catch((data) => data);

  return response;
}

export const formatarCpfCnpj = (cpfCnpj) => {
  if (cpfCnpj.length <= 11) {
    return cpfCnpj
      .replace(/[^\d]/g, "")
      .replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
  } else if (cpfCnpj.length >= 14) {
    return cpfCnpj
      .replace(/[^\d]/g, "")
      .replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, "$1.$2.$3/$4-$5");
  }
};

export const formatarDinheiroBrasil = (value) => {
  if (value === undefined || value === null) {
    return 0;
  }

  return new Intl.NumberFormat("pt-BR", {
    style: "currency",
    currency: "BRL",
  }).format(value);
};

export const formatarData = (dataString) => {
  if (!dataString) return "-";

  const data = new Date(dataString);
  const dia = String(data.getDate()).padStart(2, "0");
  const mes = String(data.getMonth() + 1).padStart(2, "0");
  const ano = data.getFullYear();

  return `${dia}/${mes}/${ano}`;
};

export const formatarDataHora = (dataString) => {
  if (!dataString) return "-";

  const data = new Date(dataString);
  const dia = String(data.getDate()).padStart(2, "0");
  const mes = String(data.getMonth() + 1).padStart(2, "0");
  const ano = data.getFullYear();
  const horas = String(data.getHours()).padStart(2, "0");
  const minutos = String(data.getMinutes()).padStart(2, "0");

  return `${dia}/${mes}/${ano} ${horas}:${minutos}`;
};

function removeHTML(html) {
  let doc = new DOMParser().parseFromString(html || "", "text/html");
  return doc.body.textContent || "";
}
