<!--
  USAGE:
  <loader size="16" fill="#888787"></loader>
-->

<template>
    <div class="overlay">
        <svg class="loader" :width="size" :height="size" viewBox="0 0 17 19" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <g class="circ">
                <g class="petal petal-1"><path d="M1667.48,4268c0,2-.92,4-2,4s-2-2-2-4A2,2,0,0,1,1667.48,4268Z" transform="translate(-1656.99 -4266)" style="fill:#654a9d;"/></g>
                <g class="petal petal-2"><path d="M1671,4270c-1.73,1-3,2.79-2.46,3.73s2.77,0.71,4.46-.27A2,2,0,0,0,1671,4270Z" transform="translate(-1656.99 -4266)" style="fill:#2693f8;"/></g>
                <g class="petal petal-3"><path d="M1671,4281c-1.73-1-3-2.79-2.46-3.73s2.77-.71,4.46.27A2,2,0,1,1,1671,4281Z" transform="translate(-1656.99 -4266)" style="fill:#fcc100;"/></g>
                <g class="petal petal-4"><path d="M1663.48,4283c0-2,.92-4,2-4s2,2,2,4A2,2,0,0,1,1663.48,4283Z" transform="translate(-1656.99 -4266)" style="fill:#4aaf4b;"/></g>
                <g class="petal petal-5"><path d="M1660,4281c1.73-1,3-2.79,2.46-3.73s-2.77-.71-4.46.27A2,2,0,0,0,1660,4281Z" transform="translate(-1656.99 -4266)" style="fill:#09bcd4;"/></g>
                <g class="petal petal-6"><path d="M1660,4270c1.73,1,3,2.79,2.46,3.73s-2.77.71-4.46-.27A2,2,0,1,1,1660,4270Z" transform="translate(-1656.99 -4266)" style="fill:#ee443b;"/></g>
            </g>
        </svg>
    </div>
</template>

<style lang="scss" scoped>
    .overlay {
        height: 100vh;
    }
    .loader {
        transform: translate(50%, 50%);
        position: absolute;
        bottom: 50%;
        right: 50%;
    }

    @keyframes boing {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
    .petal {
        position: relative;
        animation: boing 1s ease infinite alternate;
        animation-delay: 0;
    }
    .petal-1 {
        transform-origin: 8.48px 3px !important;
    }
    .petal-2 {
        transform-origin: 14.15px 6.25px !important;
    }
    .petal-3 {
        transform-origin: 14.15px 12.76px !important;
    }
    .petal-4 {
        transform-origin: 8.5px 15.98px !important;
    }
    .petal-5 {
        transform-origin: 2.88px 12.76px !important;
    }
    .petal-6 {
        transform-origin: 2.94px 6.19px !important;
    }
    .circ {
        animation: boing 3s ease infinite alternate;
        transform-origin: 50% 50% !important;
        animation-delay: 0;
    }
</style>

<script>
  export default {
      props: {
          fill: {
              default: '#888787'
          },

          size: {
              default: '24'
          }
      }
  };
</script>
