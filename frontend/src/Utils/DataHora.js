import moment from "moment";

export const dataHora = (value) => moment(value).format("DD/MM/YYYY HH:mm:ss");
export const data = (value) => moment(value).format("DD/MM/YYYY");
export const hora = (value) => moment(value).format("HH:mm:ss");
export const horaSemSegundos = (value) => moment(value).format("HH:mm");
