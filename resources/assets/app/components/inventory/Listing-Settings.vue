<template>
    <span>
        <modal
                use_header="false"
                :modal_id="modal_id"
        >
            <template slot="modal_header">
                <h6 class="m-b-0">Optional settings for listing to facebook</h6>
            </template>
            <template slot="modal_body">
                <div class="form-group row">
                    <div class="col-md-6">
                        <label class="control-label">Date to list the items</label><button type="button" @click="clearOption('ends_at', 'options_ends_at', $event)" class="btn-link btn-text text-primary text-xs">Clear</button>
                        <input @blur="updateDateTimeEl('ends_at', $event)" class="form-control needs-datetimepicker" id="options_ends_at"  name="options[ends_at]" type="text">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">Date-range items can be claimed </label><button type="button" @click="clearOption('available_at', 'options_available_at', $event)" class="btn-link btn-text text-primary text-xs">Clear</button>
                    <input @blur="updateDateTimeEl('available_at', $event)" class="form-control needs-datetimepicker" id="options_available_at" name="options[available_at]" type="text">
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input
                                    id="options_include_text"
                                    v-model="$data.options.include_link"
                                    name="options[include_link]"
                                    type="checkbox"
                            >
                            Include link in description
                        </label>
                    </div>
                </div>
            </template>
            <template slot="modal_footer">
                <button class="btn primary btn-sm" @click="saveOptions" type="button">Save</button>
                <button class="btn white btn-sm" @click="clearOptions" type="button">Clear</button>
            </template>
        </modal>
    </span>
</template>
<script>
    import Modal from '../Modal.vue';
    export default{
        props: {
            modal_id: {
                default: 'kbdl-mdl-listings',
                type: String,
            }
        },
        data(){
            return {
                options : {
                    ends_at: null,
                    available_at: null,
                    include_link: true,
                }
            }
        },
        mounted(){
            this.registerDateTimePicker();
        },
        created(){
            $Bus.$on('listing.options:get', ()=>{
                return this.options;
            });
        },
        methods: {
            saveOptions(event){
                event.preventDefault();
                $('#'+this.modal_id).modal('hide');
                $Bus.$emit('listing.options:saved', this.options);
            },
            clearOption(option, optionElId, event){
                const $el = $('#'+optionElId);
                $el.val('').trigger('change');
                this.options[option] = null;
            },
            clearOptions(){
                this.options = {
                    ends_at: null,
                    available_at: null,
                    include_link: false,
                }
                $('.needs-datetimepicker').val('').trigger('change');
            },
            registerDateTimePicker(){
                this.$nextTick(function(){
                    $('input.needs-datetimepicker').datetimepicker({
                        format: "MM/DD/YYYY hh:mmA",
                        minDate: moment().add('1', 'hour'),
                        icons: {
                            time: 'fa fa-clock-o fa-lg',
                            clear: 'fa fa-times-circle-o',
                            date: 'fa fa-lg fa-calendar',
                            up: 'fa fa-chevron-up',
                            down: 'fa fa-chevron-down',
                            previous: 'fa fa-chevron-left',
                            next: 'fa fa-chevron-right'
                        }
                    });
                });
            },
            updateDateTimeEl(option, event){
                const $el = $(event.target);
                this.options[option] = $el.val();
            }
        },
        components: {
            'modal': Modal
        }
    }
</script>
