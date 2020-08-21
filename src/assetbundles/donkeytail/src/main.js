__webpack_public_path__ = 'http://localhost:8080/' // eslint-disable-line

import Vue from 'vue'
import DonkeytailCanvas from './components/DonkeytailCanvas.vue'
import DonkeytailPin from './components/DonkeytailPin.vue'
import './app.css'

Vue.config.productionTip = false

const VueDonkeytailPlugin = {
  install(Vue) {
    Vue.component('donkeytail-canvas', DonkeytailCanvas)
    Vue.component('donkeytail-pin', DonkeytailPin)
  },
}

if (typeof window !== 'undefined' && window.Vue) {
  if (process.env.NODE_ENV == 'development') {
    Vue.use(VueDonkeytailPlugin)
  } else {
    window.Vue.use(VueDonkeytailPlugin)
  }
}

const mainVue = e => {
  return {
    el: `${e.detail}`,
    data() {
      return {
        pinElementType: 'entries',
      }
    },
    mounted() {
      const pinElementTypeSelect = document.querySelector(
        `${e.detail} .pinElementType select`,
      )
      if (pinElementTypeSelect) {
        pinElementTypeSelect.addEventListener('change', event => {
          this.pinElementType = event.target.value
        })
      }
    },
  }
}

window.addEventListener(
  'build',
  function(e) {
    if (process.env.NODE_ENV == 'development') {
      // Dev mode for Vue devtools & debugging
      // Set DONKEYTAIL_DEBUG=true in project .env
      new Vue(mainVue(e))
    } else {
      new window.Vue(mainVue(e))
    }
  },
  false,
)
