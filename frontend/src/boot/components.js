import { boot } from "quasar/wrappers";

import LIcon from "src/components/LIcon.vue";
import Modal from "src/components/Modal.vue";
import VLabel from "src/components/VLabel.vue";
// import VCnpj from "src/components/VCnpj.vue";
// import VTelefone from "src/components/VTelefone.vue";
// import VCep from "src/components/VCep.vue";
// import VStatusBadge from "src/components/VStatusBadge.vue";
// import VEmptyState from "src/components/VEmptyState.vue";
// import VSelectEstado from "src/components/VSelectEstado.vue";
// import VSelectBanco from "src/components/VSelectBanco.vue";

export default boot(({ app }) => {
  app.component("l-icon", LIcon);
  app.component("modal", Modal);
  app.component("v-label", VLabel);
  // app.component("v-password", VPassword);
  // app.component("v-cpf", VCpf);
  // app.component("v-cnpj", VCnpj);
  // app.component("v-telefone", VTelefone);
  // app.component("v-cep", VCep);
  // app.component("v-status-badge", VStatusBadge);
  // app.component("v-empty-state", VEmptyState);
  // app.component("v-select-estado", VSelectEstado);
  // app.component("v-select-banco", VSelectBanco);
});
