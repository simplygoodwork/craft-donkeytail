<template>
  <div
    ref="pinRef"
    class="-ml-2 -mt-2 h-4 w-4 rounded-full border-white shadow-sm border-2 border-solid border-opacity-90 bg-black bg-opacity-50 absolute z-10 top-0 left-0 cursor-move flex items-center justify-center"
  >
    <div
      class="h-1 w-1 rounded-full bg-white bg-opacity-90"
      style="margin: 0;"
    ></div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue';
import Draggabilly from 'draggabilly';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';

export default {
  props: {
    canvasId: {
      type: String,
      required: true,
    },
    pin: {
      type: Object,
      required: true,
    },
  },
  setup(props, { emit }) {
    const pinRef = ref(null);
    const canvas = ref(null);
    const draggie = ref(null);
    const tippyInstance = ref(null);
    const showTippy = ref(false);
    const position = ref({ x: 0, y: 0 });

    const setPosition = () => {
      const canvasWidth = canvas.value.getBoundingClientRect().width;
      const canvasHeight = canvas.value.getBoundingClientRect().height;

      const xPos = (position.value.x * canvasWidth) / 100;
      const yPos = (position.value.y * canvasHeight) / 100;
      draggie.value.setPosition(xPos, yPos);
    };

    onMounted(() => {
      canvas.value = document.getElementById(props.canvasId);

      position.value.x = props.pin.x;
      position.value.y = props.pin.y;
      showTippy.value = props.pin.showTippy;
      
      console.log(pinRef)
      draggie.value = new Draggabilly(pinRef.value, {
        containment: canvas.value,
      });

      tippyInstance.value = tippy(pinRef.value, {
        content: props.pin.label,
        moveTransition: 'transform 0.35s ease-in-out',
      });

      if (position.value.x !== 0 || position.value.y !== 0) {
        setPosition();

        window.addEventListener('resize', setPosition);

        new ResizeObserver(setPosition).observe(
          document.getElementById('content-container')
        );

        const tabs = document.getElementById('tabs');
        if (tabs) {
          tabs.addEventListener('click', setPosition);
        }
      }

      draggie.value.on('dragEnd', function () {
        const buffer = 0;
        const xPos = this.position.x + buffer;
        const yPos = this.position.y + buffer;
        const canvasWidth = canvas.value.getBoundingClientRect().width;
        const canvasHeight = canvas.value.getBoundingClientRect().height;

        const xPer = (xPos / canvasWidth) * 100;
        const yPer = (yPos / canvasHeight) * 100;

        position.value.x = Math.max(0, Number(xPer.toFixed(3)));
        position.value.y = Math.max(0, Number(yPer.toFixed(3)));
        emit('positioned', {
          id: props.pin.id,
          x: position.value.x,
          y: position.value.y,
        });
      });
    });

    onUnmounted(() => {
      window.removeEventListener('resize', setPosition);
    });

    return { pinRef };
  },
};
</script>