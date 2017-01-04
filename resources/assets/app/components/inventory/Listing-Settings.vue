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
                <div class="form-group m-b-1">
                    <div class="card b p-a dker">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label">Date to list the items</label><button type="button" @click="clearOption('list_at', 'options_list_at', $event)" class="btn-link btn-text text-primary text-xs">Clear</button>
                                <input @blur="updateDateTimeEl('list_at', $event)" placeholder="optional" class="form-control needs-datetimepicker" id="options_list_at"  name="options[list_at]" type="text">
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Date to remove the listing</label><button type="button" @click="clearOption('remove_at', 'options_remove_at', $event)" class="btn-link btn-text text-primary text-xs">Clear</button>
                                <input @blur="updateDateTimeEl('remove_at', $event)" placeholder="optional" class="form-control needs-datetimepicker" id="options_remove_at"  name="options[remove_at]" type="text">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group m-b-1">
                    <div class="card b p-a dker">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label">Start date for claiming</label><button type="button" @click="clearOption('available_at', 'options_available_at', $event)" class="btn-link btn-text text-primary text-xs">Clear</button>
                                <input @blur="updateDateTimeEl('available_at', $event)" placeholder="optional" class="form-control needs-datetimepicker" id="options_available_at" name="options[available_at]" type="text">
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Last date for claiming</label><button type="button" @click="clearOption('available_until', 'options_available_until', $event)" class="btn-link btn-text text-primary text-xs">Clear</button>
                                <input @blur="updateDateTimeEl('available_until', $event)"
                                       class="form-control needs-datetimepicker"
                                       id="options_available_until" name="options[available_until]" placeholder="optional" type="text">
                            </div>
                        </div>
                    </div>
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
                            Include link to Kabooodle claim page, in description
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
                    list_at: null,
                    remove_at:null,
                    available_at: null,
                    available_until: null,
                    include_link: true,
                }
            }
        },
        mounted(){
            this.registerDateTimePicker();
        },
        created(){
            $Bus.$on('listing.options:get', ()=>{
                $Bus.$emit('listing.options:saved', this.options);
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
                    list_at: null,
                    remove_at: null,
                    available_at: null,
                    available_until: null,
                    include_link: false,
                }
                $('.needs-datetimepicker').val('').trigger('change');
            },
            registerDateTimePicker(){
                this.$nextTick(function(){
                    let minDate = moment().add('1', 'hour');
                    let options = {
                        format: "MM/DD/YYYY hh:mma",
                        allowInputToggle: true,
                        useCurrent: false,
                        defaultDate: minDate,
                        minDate: minDate,
                        icons: {
                            time: 'fa fa-clock-o fa-lg',
                            clear: 'fa fa-times-circle-o',
                            date: 'fa fa-lg fa-calendar',
                            up: 'fa fa-chevron-up',
                            down: 'fa fa-chevron-down',
                            previous: 'fa fa-chevron-left',
                            next: 'fa fa-chevron-right'
                        }
                    };

                    $('input.needs-datetimepicker').datetimepicker(options);

                    // hack for clearing the inputs, except, checkbox.
                    this.clearOption('list_at', 'options_list_at');
                    this.clearOption('remove_at', 'options_remove_at');
                    this.clearOption('available_at', 'options_available_at');
                    this.clearOption('available_until', 'options_available_until');


                    $("#options_list_at").on("dp.change", function (e) {
                        $('#options_remove_at').data("DateTimePicker").minDate(e.date);
                    });
                    $("#options_remove_at").on("dp.change", function (e) {
                        $('#options_list_at').data("DateTimePicker").maxDate(e.date);
                    });
                    $("#options_available_at").on("dp.change", function (e) {
                        $('#options_available_until').data("DateTimePicker").minDate(e.date);
                    });
                    $("#options_available_until").on("dp.change", function (e) {
                        $('#options_available_at').data("DateTimePicker").maxDate(e.date);
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
