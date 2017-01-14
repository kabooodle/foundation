<template>
    <div class="modal" :id="modal_id" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="row-col h-v">
            <div class="row-cell v-m">
                <div class="modal-dialog" role="document">
                    <div class="box white r">
                        <div class="box-header b-0">
                            <button v-if="display_header_close_btn" type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h6 v-if="!this.$slots['modal_header']" class="m-b-0">Title</h6>
                            <slot name="modal_header"></slot>
                        </div>
                        <div class="box-divider"></div>
                        <div class="modal-body box-body">
                            <slot name="modal_body"></slot>
                        </div>
                        <div class="modal-footer box-footer">
                            <button v-if="!this.$slots['modal_footer']" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <slot name="modal_footer"><div v-el:modal_footer></div></slot>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default{
        props: {
            modal_id: {
                type: String,
                required: true
            },
            use_header_close: {
                default: false
            }
        },
        computed: {
            display_header_close_btn(){
                return (this.use_header_close === 'true' || this.use_header_close === true);
            }
        }
    }
</script>
