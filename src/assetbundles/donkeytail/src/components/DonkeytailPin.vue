<template>
  <div
    ref="pin"
    class="-ml-2 -mt-2 h-4 w-4 rounded-full border-white shadow-sm border-2 border-solid border-opacity-90 bg-black bg-opacity-50 absolute z-10 top-0 left-0 cursor-move flex items-center justify-center"
  >
    <div
      class="h-1 w-1 rounded-full bg-white bg-opacity-90"
      style="margin: 0;"
    ></div>
  </div>
</template>

<script>
import Draggabilly from 'draggabilly'
import tippy from 'tippy.js'
import 'tippy.js/dist/tippy.css'
export default {
  props: ['canvas-id', 'pin'],
  data() {
    return {
      draggie: null,
      canvas: null,
      position: {
        x: 0,
        y: 0,
      },
    }
  },
  methods: {
    setPosition() {
      const canvasWidth = this.canvas.getBoundingClientRect().width
      const canvasHeight = this.canvas.getBoundingClientRect().height

      const xPos = (this.position.x * canvasWidth) / 100
      const yPos = (this.position.y * canvasHeight) / 100
      this.draggie.setPosition(xPos, yPos)
    },
  },
  mounted() {
    const self = this
    this.canvas = document.getElementById(self.canvasId)

    this.position.x = this.pin.x
    this.position.y = this.pin.y

    this.draggie = new Draggabilly(this.$refs.pin, {
      containment: self.canvas,
    })
    tippy(self.$refs.pin, {
      content: self.pin.label,
      moveTransition: 'transform 0.35s ease-in-out',
    })

    if (this.position.x !== 0 || this.position.y !== 0) {
      window.addEventListener('resize', this.setPosition)
      this.setPosition()
    }

    // bind event listener
    this.draggie.on('dragEnd', function() {
      const buffer = 0
      const xPos = this.position.x + buffer
      const yPos = this.position.y + buffer
      const canvasWidth = self.canvas.getBoundingClientRect().width
      const canvasHeight = self.canvas.getBoundingClientRect().height

      const xPer = (xPos / canvasWidth) * 100
      const yPer = (yPos / canvasHeight) * 100

      self.position.x = Math.max(0, Number(xPer.toFixed(3)))
      self.position.y = Math.max(0, Number(yPer.toFixed(3)))
      self.$emit('positioned', {
        id: self.pin.id,
        x: self.position.x,
        y: self.position.y,
      })
    })
  },
  destroyed() {
    window.removeEventListener('resize', this.setPosition)
  },
}
</script>
