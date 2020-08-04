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
  window.Vue.use(VueDonkeytailPlugin)
}

if (window.DONKEYTAIL_DEBUG == true) {
  // Dev mode for Vue devtools & debugging
  // Set DONKEYTAIL_DEBUG=true in project .env
  window.addEventListener(
    'build',
    function(e) {
      new Vue({ el: e.detail })
    },
    false,
  )
}
