import { createApp, defineCustomElement } from 'vue';
import DonkeytailCanvas from './components/DonkeytailCanvas.vue'
import DonkeytailPin from './components/DonkeytailPin.vue'

import './app.css'

const VueDonkeytailPlugin = {
  install(app) {
    app.component('donkeytail-canvas', DonkeytailCanvas);
    app.component('donkeytail-pin', DonkeytailPin);
  },
};

const app = createApp({});

// Use the plugin globally
app.use(VueDonkeytailPlugin);

const allFields = document.querySelectorAll('.donkeytail-field-wrapper');
allFields.forEach((field) => {
  app.mount(`#${field.id}`);
})

// const mainVue = e => ({ el: e.detail });

// window.addEventListener(
//   'build',
//   function (e) {
//     console.log('lego')
//     if (process.env.NODE_ENV == 'development') {
//       // Dev mode for Vue devtools & debugging
//       // Set DONKEYTAIL_DEBUG=true in project .env
//       app.mount(mainVue(e));
//     } else {
//       createApp(mainVue(e)).mount();
//     }
//   },
//   false
// );