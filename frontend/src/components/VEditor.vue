<template>
  <Editor v-model="editorValue" :init="config"></Editor>
</template>

<script setup>
import Editor from "@tinymce/tinymce-vue";
import { ref } from "vue";

defineOptions({
  name: "EditorBasico",
});
import "tinymce/tinymce";
import "tinymce/models/dom";
// TinyMCE-Vue
// LANG
import "tinymce-i18n/langs7/pt_BR";

import "tinymce/skins/ui/oxide/skin.css";
import "tinymce/themes/silver";

// Icon
import "tinymce/icons/default";

import "tinymce/plugins/emoticons";
import "tinymce/plugins/emoticons/js/emojis.js";
import "tinymce/plugins/table";
import "tinymce/plugins/fullscreen/plugin";
import "tinymce/plugins/lists/plugin";
import "tinymce/plugins/link/plugin";
import "tinymce/plugins/image/plugin";
import "tinymce/plugins/table/plugin";
import "tinymce/plugins/code/plugin";
import "tinymce/plugins/help/plugin";
import "tinymce/plugins/wordcount/plugin";
import "tinymce/plugins/fullscreen/plugin";
import "tinymce/plugins/anchor/plugin";
import "tinymce/plugins/insertdatetime/plugin";
import "tinymce/plugins/media/plugin";
import "tinymce/plugins/nonbreaking/plugin";
import "tinymce/plugins/pagebreak/plugin";
import "tinymce/plugins/save/plugin";
import "tinymce/plugins/searchreplace/plugin";
import "tinymce/plugins/visualblocks/plugin";
import "tinymce/plugins/visualchars/plugin";
import "tinymce/plugins/autosave/plugin";
import "tinymce/plugins/charmap/plugin";
import "tinymce/plugins/emoticons/plugin";
import "tinymce/plugins/preview/plugin";
import "tinymce/plugins/autoresize/plugin";
import "tinymce/plugins/quickbars/plugin";
import "tinymce/plugins/codesample/plugin";
const props = defineProps({
  altura: {
    type: Number,
    default: 200,
  },
  idioma: {
    type: String,
    default: "pt_BR",
  },
  menu: {
    type: Boolean,
    default: false,
  },

  variaveis: {
    type: Array,
    default: () => [],
  },
});

const config = ref({
  plugins: [
    "fullscreen",
    "preview",
    "searchreplace",
    "visualblocks",
    "visualchars",
    "image",
    "link",
    "media",
    "table",
    "charmap",
    "pagebreak",
    "nonbreaking",
    "anchor",
    "insertdatetime",
    "lists",
    "wordcount",
    "charmap",
    "emoticons",
    "autosave",
    "code",
    "codesample",
    "styleselect",
    "fullpage ",
    "emoticons",
    "quickbars",
  ],
  // 'fullscreen | styles | list | fontFamily | fontsize fontsizeinput | undo redo | bold italic underline | forecolor backcolor | alignleft aligncenter alignright alignjustify lineheight | image lists | removeformat | table | selectall | copy  ',
  // "undo redo print spellcheckdialog formatpainter | blocks fontfamily fontsize | bold italic underline forecolor backcolor | link image | alignleft aligncenter alignright alignjustify lineheight pagebreak | checklist bullist numlist indent outdent | removeformat | spellchecker language | a11ycheck ",
  toolbar: [
    // "fullscreen | blocks fontfamily fontsize | bold italic underline strikethrough forecolor backcolor removeformat | undo redo | table | alignleft aligncenter alignright alignjustify | bullist numlist",
    "fullscreen bold italic underline forecolor backcolor alignleft aligncenter alignright alignjustify bullist numlist removeformat copy paste table selectall code",
  ],
  height: props.altura,
  language: props.idioma,
  // branding: false,
  skin: false,
  content_css: "default",
  // Define Arial como fonte padrão
  content_style: `
    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 14px;
    }
  `,

  // Opcional: define opções no seletor de fonte
  font_family_formats: `
    Arial=arial,helvetica,sans-serif;
    Times New Roman=times new roman,times;
    Courier New=courier new,courier;
    Calibri=calibri;
    Verdana=verdana
  `,
  menubar: props.menu,
  // skin_url: "/tinymce/skins/ui/jam/skin.min.css",
  // content_css: "/tinymce/skins/ui/jam/content.min.css",
  // icons_url: "/tinymce/icons/jam/icons.js",
  // icons: "jam",
  // selector: "textarea#premiumskinsandicons-jam",
});
</script>