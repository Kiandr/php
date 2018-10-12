<template>
  <textarea class="form-textarea tinymce w1/1" ref="textarea" :id="id">{{ content }}</textarea>
</template>

<script>
    /* global tinymce:true */

    import defaultPlugins from 'plugins/tinymce/defaultPlugins';
    import getTinyMCEOpts from 'plugins/tinymce/getTinyMCEOpts';

    export default {
        props: {
            id: {
                type: String,
                required: true
            },
            value: {
                type: String,
                default: ''
            }
        },
        data() {
            return {
                content: '',
                editor: null,
                cTinyMce: null,
                checkerTimeout: null,
                isTyping: false
            };
        },
        mounted() {
            this.content = this.value || '';
            this.initEditor();
        },
        beforeDestroy() {
            this.editor.destroy();
        },
        watch: {
            value(newValue) {
                if (!this.isTyping){
                    if (this.editor !== null) {
                        this.editor.setContent(newValue);
                    } else {
                        this.content = newValue;
                    }
                }
            }
        },
        methods: {
            initEditor() {
                const tinyMCEOptions = getTinyMCEOpts(this.$el, {
                    selector: `#${this.id}`,
                    plugins: defaultPlugins.filter(v => {
                        return v !== 'autoresize';
                    }),
                    init_instance_callback: editor => {
                        this.editor = editor;
                        editor.on('KeyUp', () => {
                            this.emitValue();
                        });
                        editor.on('Change', () => {
                            if (this.editor.getContent() !== this.value) {
                                this.emitValue();
                            }
                        });
                        editor.on('init', () => {
                            editor.setContent(this.content);
                            this.$emit('input', this.content);
                        });
                    }
                });
                tinymce.init(tinyMCEOptions);
            },
            emitValue() {

                // You still typing?
                this.isTyping = true;
                if (this.checkerTimeout !== null) {
                    clearTimeout(this.checkerTimeout);
                }
                this.checkerTimeout = setTimeout(()=> {
                    this.isTyping = false;
                }, 300);

                // Truth is in the editor's content
                const value = this.editor.getContent();
                this.$emit('input', value);

            }
        }
    };
</script>
