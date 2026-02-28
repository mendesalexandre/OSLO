import { boot } from 'quasar/wrappers'
import { Notify, Loading, QInput, QCard, QSelect, QBtn } from 'quasar'

const defaultQInput = {
  outlined: true,
  dense: true,
  color: 'primary',
  hideBottomSpace: true,
}

const defaultQSelect = {
  outlined: true,
  dense: true,
  color: 'primary',
  clearIcon: 'eva-close-outline',
  dropdownIcon: 'keyboard_arrow_down',
}

const defaultQCard = {
  outlined: true,
}

const defaultQBtn = {
  outlined: true,
}

Notify.setDefaults({
  position: 'top-right',
  timeout: 2500,
  textColor: 'white',
  actions: [{ icon: 'close', color: 'white', round: true }],
  progress: true,
  icon: 'eva-info-outline',
})

Loading.setDefaults({
  spinnerColor: 'white',
  message: 'Carregando...',
  messageClass: 'text-h6',
})

function mapDefaultsToObject(defaultProps, objectType) {
  Object.keys(defaultProps).forEach((prop) => {
    objectType.props[prop] =
      Array.isArray(objectType.props[prop]) === true ||
      typeof objectType.props[prop] === 'function'
        ? { type: objectType.props[prop], default: defaultProps[prop] }
        : { ...objectType.props[prop], default: defaultProps[prop] }
  })
}

export default boot(() => {
  mapDefaultsToObject(defaultQInput, QInput)
  mapDefaultsToObject(defaultQSelect, QSelect)
  mapDefaultsToObject(defaultQCard, QCard)
  mapDefaultsToObject(defaultQBtn, QBtn)
})
