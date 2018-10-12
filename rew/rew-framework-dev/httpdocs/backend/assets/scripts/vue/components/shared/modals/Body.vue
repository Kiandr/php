<template>
  <div class="modal--body">
    <div @click="close">
      <button class="close">
        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 24 24" xml:space="preserve" width="24" height="24">
          <path fill="currentColor" d="M19.7,4.3c-0.4-0.4-1-0.4-1.4,0L12,10.6L5.7,4.3c-0.4-0.4-1-0.4-1.4,0s-0.4,1,0,1.4l6.3,6.3l-6.3,6.3 c-0.4,0.4-0.4,1,0,1.4C4.5,19.9,4.7,20,5,20s0.5-0.1,0.7-0.3l6.3-6.3l6.3,6.3c0.2,0.2,0.5,0.3,0.7,0.3s0.5-0.1,0.7-0.3 c0.4-0.4,0.4-1,0-1.4L13.4,12l6.3-6.3C20.1,5.3,20.1,4.7,19.7,4.3z"></path>
        </svg>
      </button>
    </div>
    <slot name="title"></slot>
    <slot name="content"></slot>
    <loader v-if="loading"></loader>
  </div>
</template>

<style lang="scss" scoped>
    /**
   * Wrapper around the contained content (header / content)
   * This is the shadowed section.
   */
    .modal--body {
        @include shadow(5);

        background-color: $modal-background-color;
        border-radius: $modal-border-radius;
        position: relative;

        max-height: 100%;
        max-width: 100%;

        transition-timing-function: cubic-bezier(.3, 0, 0, 1.3);
        transition-duration: 400ms;
        transform: translateY(40%);
        transition-delay: 0ms;
        opacity: 0;

        will-change: transform, opacity;
        overflow-x: hidden;
        overflow-y: auto;

        /**
     * Have this element appear when the containing modal is marked with `.-is-open`
     */
        .modal.-is-open & {
            transition-delay: 150ms;
            transform: none;
            opacity: 1;
        }

        .close {
            position: absolute;
            cursor: pointer;
            right: 1.25rem;
            top: 1.25rem;
            z-index: 5;
            border: 0;
            background-color: transparent;

            color: $black;
            opacity: .4;
            border: 0;
            background-color: transparent;

            &:hover {
                opacity: .8;
            }

            &:focus {
                outline: none;
                background-color: transparentize($gray-600, .92);
            }
        }

        /deep/ .loader {
            transform: translateY(0);
        }

        /* Overlay for <loader> */
        /deep/ .overlay {
            background: transparentize($white, 0.4);
            position: absolute;
            bottom: 0;
            right: 0;
            left: 0;
            top: 0;
            z-index: 10;
            justify-content: center;
            align-items: center;
            display: flex;
        }
    }
</style>

<script>
    import store from 'store';

    export default {
        methods: {
            close: function () {
                store.dispatch('modal/close');
            }
        },
        computed: {
            loading: function () {
                return store.getters['modal/isLoading'];
            }
        }
    };
</script>
