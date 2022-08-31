<template>
  <div
    :id="`${namespacedId}-canvas`"
    ref="canvas"
    class="rounded leading-0 overflow-hidden relative"
  >
    <img ref="image" :src="canvasUrl" class="w-full" />
    <div v-if="showPins">
      <donkeytail-pin
        v-for="pin in pins"
        :key="`pin-${pin.id}`"
        :pin="pin"
        :canvas-id="`${namespacedId}-canvas`"
        @positioned="setPinPosition($event)"
      ></donkeytail-pin>
    </div>
    <div v-for="pin in pins" :key="`pinput-${pin.id}`">
      <input type="hidden" :name="`${name}[meta][${pin.id}]`" />
      <input
        type="hidden"
        :name="`${name}[meta][${pin.id}][id]`"
        :value="pin.id"
      />
      <input
        type="hidden"
        :name="`${name}[meta][${pin.id}][label]`"
        :value="pin.label"
      />
      <input
        type="hidden"
        :name="`${name}[meta][${pin.id}][x]`"
        :value="pin.x"
      />
      <input
        type="hidden"
        :name="`${name}[meta][${pin.id}][y]`"
        :value="pin.y"
      />
    </div>
  </div>
</template>

<script>
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
  data() {
    return {
      canvasUrl: false,
      showPins: false,
      pins: [],
    }
  },
  methods: {
    getCanvasUrl(assetId) {
      const self = this
      window
        .axios({
          method: 'post',
          url: window.Craft.getActionUrl('donkeytail/default/get-asset'),
          data: {
            assetId: assetId,
            requestId: 1,
          },
          headers: { 'X-CSRF-Token': window.csrfToken },
        })
        .then(function(response) {
          self.canvasUrl = response.data

          self.$refs.image.onload = function() {
            $(`#${self.namespacedId}-pane`).velocity(
              {
                height: $(`#${self.namespacedId}-pane`).get(0).scrollHeight,
                padding: '24px',
                opacity: 1,
              },
              400,
              'easeInOut',
              function() {
                $(this).css('height', 'auto')
                self.showPins = true
              },
            )
          }
        })
        .catch(function(error) {
          console.log(error)
        })
    },
    setPinPosition(payload) {
      let pinToUpdate = this.pins.findIndex(x => x.id === payload.id)
      Object.assign(this.pins[pinToUpdate], payload)
    },
  },
  mounted() {
    const self = this

    if (self.value) {

      if(self.image){
        self.canvasUrl = self.image
	      self.showPins = true
      }else {
        self.getCanvasUrl(self.value)
      }

      // If meta prop is passed, then transform data & preset pins
      if (self.meta && Object.keys(self.meta).length) {
        let initialPins = []
        for (const i of Object.keys(self.meta)) {
          self.meta[i].id = Number(self.meta[i].id)
          self.meta[i].x = Number(self.meta[i].x)
          self.meta[i].y = Number(self.meta[i].y)
          initialPins.push(self.meta[i])
        }
        self.pins = initialPins
      }
    }
    $(document).ready(function() {
      // On Canvas Selection
      $(`#${self.namespacedId}-canvasId`)
        .data('elementSelect')
        .on('selectElements', function(e) {
          console.log(e)
          self.getCanvasUrl(e.elements[0].id)
        })

      // On Canvas Removal
      $(`#${self.namespacedId}-canvasId`)
        .data('elementSelect')
        .on('removeElements', function(e) {
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
        .on('selectElements', function(e) {
          let difference = e.elements.filter(x => !self.pins.includes(x))
          difference.forEach(element => {
            self.pins.push({
              id: element.id,
              label: element.label,
              x: 50,
              y: 50,
            })
          })
        })

      // On Pin Removal
      $(`#${self.namespacedId}-pins`)
        .data('elementSelect')
        .on('removeElements', function(e) {
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
  },
}
</script>
