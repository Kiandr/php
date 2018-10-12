<template>
  <div class="form-group field">
    <slot></slot>
  </div>
</template>

<style lang="scss" scoped>
    .form-group {
        position: relative;

        /**
     * todo: temporary styles
     */
        /deep/ .form-error {
            position: relative;
            z-index: 10;
            display: inline-block;
            max-width: 450px;
            padding: 5px 8px;
            margin: -6px 0 0;
            font-size: 13px;
            font-weight: normal;
            border-style: solid;
            border-width: 1px;
            border-radius: 3px;
            color: #911;
            background-color: #fcdede;
            border-color: #d2b2b2;

            &::before,
            &::after {
                position: absolute;
                bottom: 100%;
                left: 10px;
                z-index: 15;
                width: 0;
                height: 0;
                pointer-events: none;
                content: " ";
                border: solid transparent;
            }

            &::before {
                border-bottom-color: #d2b2b2;
                margin-left: -1px;
                border-width: 6px;
            }

            &::after {
                border-bottom-color: #fcdede;
                border-width: 5px;
            }
        }
    }
</style>