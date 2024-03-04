<template>
  <div :id="`${namespacedId}-canvas`" ref="canvas" class="rounded leading-0 overflow-hidden relative">
    <img ref="image" :src="canvasUrl" class="w-full" />
    <div v-if="showPins">
      <donkeytail-pin v-for="pin in pins" :key="`pin-${pin.id}`" :pin="pin" :canvas-id="`${namespacedId}-canvas`"
        @positioned="setPinPosition($event)"></donkeytail-pin>
    </div>
    <div v-for="pin in pins" :key="`pinput-${pin.id}`">
      <input type="hidden" :name="`${name}[meta][${pin.id}]`" />
      <input type="hidden" :name="`${name}[meta][${pin.id}][id]`" :value="pin.id" />
      <input type="hidden" :name="`${name}[meta][${pin.id}][label]`" :value="pin.label" />
      <input type="hidden" :name="`${name}[meta][${pin.id}][x]`" :value="pin.x" />
      <input type="hidden" :name="`${name}[meta][${pin.id}][y]`" :value="pin.y" />
      <input type="hidden" :name="`${name}[meta][${pin.id}][showTippy]`" :value="pin.showTippy" />
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';

export default {
  props: {
    namespacedId: {
      type: String,
      required: true,
    },
    name: {
      type: String,
      required: true,
    },
    value: {
      type: Number,
    },
    image: {
      type: String,
    },
    meta: {
      type: [Object, Array],
    },
  },
  setup(props) {
    const canvas = ref(null);
    const imageRef = ref(null);
    const canvasUrl = ref(props.image);
    const showPins = ref(false);
    const pins = ref([]);

    const getCanvasUrl = (assetId) => {
      const self = this
      window.axios({
        method: 'post',
        url: window.Craft.getActionUrl('donkeytail/default/get-asset'),
        data: {
          assetId: assetId,
          requestId: 1,
        },
        headers: { 'X-CSRF-Token': window.csrfToken },
      })
      .then(function (response) {
        self.canvasUrl = response.data

        self.$refs.image.onload = function () {
          $(`#${self.namespacedId}-pane`).velocity(
            {
              height: $(`#${self.namespacedId}-pane`).get(0).scrollHeight,
              padding: '24px',
              opacity: 1,
            },
            400,
            'easeInOut',
            function () {
              $(this).css('height', 'auto')
              self.showPins = true
            },
          )
        }
      })
      .catch(function (error) {
        console.log(error)
      })
    }

    const setPinPosition = (payload) => {
      const pinToUpdate = pins.value.findIndex(x => x.id === payload.id);
      if (pinToUpdate !== -1) {
        Object.assign(pins.value[pinToUpdate], payload);
      }
    };

    // Return the setup method's variables and functions
    return {
      canvas,
      imageRef,
      canvasUrl,
      showPins,
      pins,
      getCanvasUrl,
      setPinPosition
    };
  },
  mounted() {
    const self = this;

    if (this.value) {
      if (this.image) {
        this.canvasUrl = this.image;
        this.showPins = true;
      } else {
        getCanvasUrl(this.value);
      }

      if (this.meta && Object.keys(this.meta).length) {
        let initialPins = [];
        for (const i of Object.keys(this.meta)) {
          self.meta[i].id = Number(self.meta[i].id);
          self.meta[i].x = Number(self.meta[i].x);
          self.meta[i].y = Number(self.meta[i].y);
          self.meta[i].showTippy = false;
          initialPins.push(self.meta[i]);
        }
        self.pins = initialPins;
      }
    }

    $(document).ready(function () {
      // On Canvas Selection
      $(`#${self.namespacedId}-canvasId`)
        .data('elementSelect')
        .on('selectElements', function (e) {
          self.getCanvasUrl(e.elements[0].id)
        })

      // On Canvas Removal
      $(`#${self.namespacedId}-canvasId`)
        .data('elementSelect')
        .on('removeElements', function (e) {
          self.canvasUrl = false

          $(`#${self.namespacedId}-pane`).velocity(
            {
              height: 0,
              padding: 0,
              opacity: 0,
            },
            {
              duration: 400,
              easing: 'easeInOut',
            },
          )
        })

      // On Pin Selection
      $(`#${self.namespacedId}-pins`)
        .data('elementSelect')
        .on('selectElements', function (e) {
          let difference = e.elements.filter(x => !self.pins.includes(x))
          difference.forEach(element => {
            self.pins.push({
              id: element.id,
              label: element.label,
              x: 50,
              y: 50,
              showTippy: false,
            })
          })
        })

      // Listen for ac
      const pinInputElementButtons = Array.from($(`#${self.namespacedId}-pins .element`))
      const pinElementObserverOptions = {
        attributes: true
      }

      const pinElementObserverCallback = (mutationList, observer) => {
        mutationList.forEach((mutation) => {
          if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
            const el = mutation.target;
            if (el.classList.contains('sel')) {
              const pinId = el.dataset.id;
              let pinToUpdate = self.pins.findIndex(pin => pin.id === parseInt(pinId))
              Object.assign(self.pins[pinToUpdate], { showTippy: true })
            } else {
              const pinId = el.dataset.id;
              let pinToUpdate = self.pins.findIndex(pin => pin.id === parseInt(pinId))
              Object.assign(self.pins[pinToUpdate], { showTippy: false })
            }
          }
        })
      }
      const pinElementObserver = new MutationObserver(pinElementObserverCallback)
      pinInputElementButtons.forEach((button) => {
        pinElementObserver.observe(button, pinElementObserverOptions)
      })

      // On Pin Removal
      $(`#${self.namespacedId}-pins`)
      .data('elementSelect')
      .on('removeElements', function (e) {
        let ids = []
        // Get IDs of items remaining in elementSelect
        for (const iterator of e.target.elementSelect.$items) {
          ids.push(Number(iterator.getAttribute('data-id')))
        }

        // Figure out which ID has been removed
        let difference = self.pins.filter(x => !ids.includes(x.id))[0]

        // Remove from pins
        let indexToRemove = self.pins.findIndex(x => x.id == difference.id)
        self.pins.splice(indexToRemove, 1)
      })
    })
  }
};
</script>
<style scoped>
</style>