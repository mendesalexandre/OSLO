import { boot } from 'quasar/wrappers'
import { vPermissao } from 'src/directives/vPermissao'

export default boot(({ app }) => {
  app.directive('permissao', vPermissao)
})
