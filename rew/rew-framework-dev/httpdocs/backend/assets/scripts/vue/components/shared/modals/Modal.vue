<template>
  <div class="modal" :class="{ '-is-open' : open }">
    <modal-body :loader="loading">
      <modal-title slot="title" v-if="title" :class="{'-has-tabs': contentTabs}">
          <span class="modal__tabs__title">{{ title }}</span>
        <div v-if="contentTabs" class="modal__tabs">
          <button v-for="(tab, index) in contentTabs" @click.native="tabSwitch(index)" is="btn" :modifiers="['ghost']" class="btn--action" :class="{ active: tab.active }" tabindex="0">
            {{ tab.title }}
          </button>
        </div>
      </modal-title>
      <modal-content slot="content" :class="{ '-no-title' : !title }">
        <div :is="content"></div>
      </modal-content>
    </modal-body>
  </div>
</template>

<style lang="scss" scoped>
    /**
     * The wrapper / background dimming effect for the modal
     * Small right around the edge to prevent it from going over the width of the web page
     */
    .modal {
        background: transparentize($black, 1);
        z-index: $modal-zindex;
        position: fixed;
        padding: 16px 16px 0;
        bottom: 0;
        right: 0;
        left: 0;
        top: 0;

        justify-content: center;
        align-items: center;
        flex-flow: row;
        display: flex;

        pointer-events: none;

        will-change: background;
        transition: 160ms;

        /**
         * Actually display it with this class.
         */
        &.-is-open {
            background: transparentize($black, 0.4);
            pointer-events: auto;
        }
    }

    .modal__tabs {
        margin-left: 32px;
    }

    .modal__tabs .btn {
        padding-left: 6px;
        padding-right: 6px;
    }

    .modal__tabs .btn.active {
        border-color: #fff;
        background-color: #fff;
        box-shadow: none;
    }

    /deep/ .modal__subtitle {
        font-weight: normal;
        color: $primary;
    }

    .modal__tabs__title {
        font-size: 16px;
        font-weight: normal;
        color: $primary;
    }

    @media (max-width: 480px) {
        .modal__tabs {
            width: 100%;
            margin-left: 0;
        }

        .modal__tabs__title {
            width: 100%;
        }

        .-has-tabs .modal__tabs__title {
            margin-bottom: 20px;
        }

        /deep/ .modal--title {
            flex-direction: column;
        }
    }
</style>

<style>
    .modal--content img {
        height: auto;
    }
</style>

<script>
    import store from 'store';

    export default {
        computed: {
            open: function () {
                return store.getters['modal/isOpen'];
            },
            loading: function () {
                return store.getters['modal/isLoading'];
            },
            title: function () {
                return store.getters['modal/getTitle'];
            },
            content: function () {
                return store.getters['modal/getContent'];
            },
            contentTabs: function () {
                return store.getters['modal/getContentTabs'];
            }
        },
        methods: {
            tabSwitch: function (tabIndex) {
                store.dispatch('modal/setActiveContent', {
                    activeContent: tabIndex
                });
            }
        }
    };
</script>
