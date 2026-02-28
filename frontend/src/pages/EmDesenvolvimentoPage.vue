<template>
  <q-page class="flex flex-center">
    <div class="text-center q-pa-xl">
      <q-icon name="construction" size="64px" color="orange-4" class="q-mb-lg" />
      <div class="text-h5 text-weight-bold text-grey-8 q-mb-sm">Em Desenvolvimento</div>
      <div class="text-body2 text-grey-6 q-mb-xl">
        Esta funcionalidade será disponibilizada em breve.
      </div>
      <q-btn
        unelevated
        color="primary"
        label="Voltar ao Início"
        :to="{ name: 'dashboard' }"
      />
    </div>
  </q-page>
</template>
