<template>
    <span>

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
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label">Photo message</label>
                            <textarea class="form-control" id="options_item_message" rows="5" v-model="$data.options.item_message" placeholder="An example: Claim here {url}"></textarea>
                            <small class="text-xs block text-muted">Pre-defined tags you can optionally use</small>
                            <button class="btn btn-clipboard white btn-xs" data-animation="false" data-clipboard-action="copy" data-clipboard-text="{price}">{price}</button>
                            <button class="btn btn-clipboard white btn-xs"  data-animation="false" data-clipboard-action="copy" data-clipboard-text="{url}">{url}</button>
                            <button class="btn btn-clipboard white btn-xs"  data-animation="false" data-clipboard-action="copy" data-clipboard-text="{style}">{style}</button>
                            <button class="btn btn-clipboard white btn-xs"  data-animation="false" data-clipboard-action="copy" data-clipboard-text="{size}">{size}</button>
                        </div>
                        <div class="col-md-6">
                            <label class="control-label">Example message (with fake data)</label>
                            <pre style="background: white; white-space: pre-wrap; word-wrap: break-word; font-family: inherit;" class="bg-white text-sm text-muted m-t-sm">{{ message_preview }}</pre>
                        </div>
                    </div>
                </div>

                <!--<button class="btn white btn-sm" :disabled="processing_listing" :class="processing_listing ? 'disabled' : null" @click="clearOptions" type="button">Clear settings</button>-->
    </span>
</template>
<script>
    import Spinny from '../Spinner.vue';
    export default{
        data(){
            return {
                processing_listing: false,
                options : {
                    list_at: null,
                    remove_at:null,
                    available_at: null,
                    available_until: null,
                    include_link: true,
                    item_message: null
                }
            }
        },
        mounted(){
            $.fn.modal.Constructor.prototype._enforceFocus = function() {};
            this.registerDateTimePicker();
        },
        created(){
            $Bus.$on('inventory:posting_listing', (status)=>{
                if (status === true) {
                    this.processing_listing = true;
                } else {
                    this.processing_listing = false;
                }
            });

            $Bus.$on('listings:saving', ()=>{
                $Bus.$emit('listings:options:get', this.options);
            });
        },
        computed: {
            message_preview(){
                if (this.options.item_message) {
                    const price = Math.floor(Math.random() * 20);
                    const url = 'kabooodle.com/i/'+randomAlphaStr(3)+' ';
                    let style = 'Amelia';
                    let size = ['XS', 'S', 'M'];
                    let text = this.options.item_message;

                    text = text.replace(/{price}/g, '$'+price);
                    text = text.replace(/{size}/g, size[Math.floor(Math.random() * size.length)]);
                    text = text.replace(/{style}/g, style);
                    text = text.replace(/{url}/g, url);

                    return text;
                }

                return this.options.item_message;
            },
        },
        methods: {
            saveOptions(event){
                event.preventDefault();
                $Bus.$emit('listing.options:saved', this.options);
                this.processing_listing = true;
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
                    item_message: null,
                    include_link: false
                }
                $('.needs-datetimepicker').val('').trigger('change');
            },
            registerDateTimePicker(){

                $('.btn-clipboard').tooltip({
                    container: 'body',
                    trigger: 'click',
                    placement: 'bottom'
                });

                var clipboard = new Clipboard('.btn-clipboard');

                clipboard.on('success', function(e) {
                    try {
                        $(e.trigger)
                                .attr('title', 'Copied!')
                                .attr('data-original-title', 'Copied!')
                                .tooltip('show');

                        setTimeout(function(){
                            $(e.trigger).tooltip('hide');
                        }, 500);
                    } catch (e) {
                        try {
                            $(e.trigger).tooltip('dispose');
                        } catch (e) {}
                    }
                    e.clearSelection();
                });

                this.$nextTick(function(){

                    let minDate = moment().add('299', 'second');
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
            },
            addVariableToMessage(text, event){
                event.preventDefault();
                console.log(text);
                let $textareaEl = $('#options_item_message');

                // in the event user has selected/highlighted text for replacement
                // get the start and end of cursor selection.
                let cursorPosStart = $textareaEl.prop('selectionStart');
                let cursorPosEnd = $textareaEl.prop('selectionEnd');

                // current value
                let v = $textareaEl.val();

                // Get the value of the selected text
                let textBefore = v.substring(0,  cursorPosStart );
                let textAfter  = v.substring( cursorPosEnd, v.length );

                // Create our new string with the newly added text
                let newText = textBefore+ text +textAfter;

                // Textarea is a v-model so set the data and it will update
                // automatically.
                this.options.item_message = newText;

                // place the cursor back in the text area.
                $textareaEl.focus();
//                $textareaEl.val( textBefore+ text +textAfter );
            }
        },
        components: {
            'spinny' : Spinny
        }
    }
</script>
