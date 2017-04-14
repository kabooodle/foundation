;
/**
 *
 */
(function($, window, document, undefined){

    /**
     *
     * @param element
     * @param options
     * @returns {S3Uploader}
     * @constructor
     */
    function S3Uploader(element, options) {
        this.element = element;
        this.$element = $(element);
        this.options = $.extend({}, $.fn.s3uploader.defaults, options);
        this.options = $.extend({}, this.options, this._parseHtmlDataAttributes(this.$element));
        this.jqXHRCollection = [];
        this.templateElements = {
            progress_container: '.js-fileupload-progress',
            progress_bar: '.progress',
            add_file_button: '.fileinput-button',
            file_upload_target: '.js-s3_fileupload',
            cancel_button: '.js-cancel_button',
            showExtendedBool: true
        };
        this._defaults = $.fn.s3uploader.defaults;
        this.init();
    }

    /**
     *
     * @type {{init: Function, setTemplate: Function, initFileUpload: Function, setProgress: Function, buttonToggler: Function, throwException: Function, log: Function, _parseHtmlDataAttributes: Function}}
     */
    S3Uploader.prototype = {
        /**
         *
         * @returns {S3Uploader}
         * @private
         */
        init: function(){
            if (!$.fn.fileupload) {
                this.throwException('missing-dependency', 'fileupload plugin required.');
            }
            this.setTemplate(this.options.templateEl);
            this.initFileUpload();
            return this;
        },
        /**
         *
         */
        setTemplate: function($el){
            this.$element.html(
                $el ? this.$element.parent().find($el) : Template.getTemplate(this.options.multiple, this.options.button_name, this.options.extended_upload_info)
            , true);
        },
        /**
         *
         */
        initFileUpload: function(){
            var that = this;
            var fileUpload = {
                url: "",
                dataType: "xml",
                type: "POST",
                dropZone: that.options.drop_zone,
                add: function (e, data) {
                    //since we are overriding the add function, in order for image-resize to work we must call the parent add()
                    $.blueimp.fileupload.prototype.options.add.call(this, e, data);
                    // if(!that.options.on_file_add(e, data)){
                    //     return false;
                    // }

                    that.buttonToggler(true);
                    that.$element.find(that.templateElements.progress_container).show();
                    that.jqXHRCollection.push(S3.upload({
                        Key: that.getKeyName(data),
                        Bucket: that.options.s3_bucket,
                        Body: data.files[0],
                        ACL: that.options.s3_acl
                    }, function (err, data) {
                        if (err) {
                            that.log('there was an error uploading the file: ', err.message);
                        } else {
                            that.log('file uploaded: ', data);
                            that.buttonToggler(false);
                            that.options.on_s3_upload(data);
                        }
                    }));
                },
                formData: {},
                success: function (data, textStatus, jqXHR) {
                    that.log('file uploaded');
                    // that.log('initiating on_s3_upload');
                    // that.options.on_s3_upload(data, textStatus, jqXHR);
                },
                done: function (e, data) {
                    that.log('done uploading files.');
                },
                fail: function(e, data, error){
                    that.log('failed uploading files.');
                    that.throwException(e.responseText, error);
                },
                always: function() {
                },
                progress: function (e, data) {
                    if (e.isDefaultPrevented()) {
                        return false;
                    }
                    var progress = Math.floor(data.loaded / data.total * 100);
                    if (data.context) {
                        that.setProgress(progress);
                    }
                },
                stop: function () {
                    that.resetProgress();
                    that.buttonToggler(false);
                }
            };
            fileUpload = $.extend(fileUpload, this.options.fileupload_options);
            that.$element.find(that.templateElements.file_upload_target).fileupload(fileUpload);
            $(this.templateElements.cancel_button).on('click', $.proxy(function(e){
                that = this;
                that.cancelAll();
            }, that));
        },
        
        getKeyName: function (data) {
            var that = this;
            return 'resources/'+that.options.s3_key_payload.user+'/'+Math.floor(new Date().getTime() / 1000)+randomAlphaStr()+'_'+data.files[0].name.toLowerCase().replace(' ', '_');
        },
        
        /**
         *
         * @param percent
         */
        setProgress: function(percent) {
            var that = this;
            that.$element.find(that.templateElements.progress_bar)
                .attr('aria-valuenow', percent).children().first()
                .css('width', percent + '%');
        },

        /**
         *
         */
        resetProgress: function() {
            var that = this;
            setTimeout(function(){
                that.$element.find(that.templateElements.progress_bar)
                    .attr('aria-valuenow', 5).children().first()
                    .css('width', '5%');
            }, 1000);
        },
        /**
         *
         * @param enable
         */
        buttonToggler: function(enable) {
            var that = this;
            var addFileButton = that.$element.find(that.templateElements.add_file_button);
            var cancelButton = that.$element.find(that.templateElements.cancel_button);
            if(enable) {
                addFileButton.hide();
                cancelButton.show();
            } else {
                cancelButton.hide();
                addFileButton.show();
            }
        },
        cancelAll: function(){
            var that = this;
            $.each(that.jqXHRCollection, function(key, jqXHR){
                jqXHR.abort();
            });
            that.buttonToggler(false);
        },
        /**
         *
         * @param exception
         * @param error
         */
        throwException: function(exception, error){
            if(typeof Bugsnag != 'undefined') {
                Bugsnag.notify("S3Uploader", exception);
            }
            this.log(exception + ' exception:', error);
        },
        /**
         *
         * @param title
         * @param data
         */
        log: function(title, data) {
            if(this.options.debug) {
                console.log('S3Uploader: ' + title);
                console.log(data);
            }
        },
        /**
         *
         * @param el
         * @returns {Array}
         * @private
         */
        _parseHtmlDataAttributes: function(el) {
            var keys = [],
                elDataAttributes = el.data();
            for (var key in this.options) {
                if (this.options.hasOwnProperty(key) && elDataAttributes.hasOwnProperty(key)) {
                    keys[key] = elDataAttributes[key];
                }
            }
            return keys;
        }
    };

    var Template = {
        /**
         * @param multipleBool
         * @param customButtonName
         * @param showExtendedBool
         * @returns {string}
         */
        getTemplate: function(multipleBool, customButtonName, showExtendedBool){
            var name = 'file';
            var multiple = '';
            var buttonName = 'Add File';
            if(multipleBool) {
                name = 'file';
                multiple = ' multiple';
                buttonName = 'Add Files';
            }
            if(customButtonName) {
                buttonName = customButtonName;
            }
            var template = '' +
                '<div class="row">' +
                '    <div class="col-sm-4">' +
                '        <span class="btn btn-primary btn-sm fileinput-button">' +
                '            <i class="fa fa-plus"></i>&nbsp;' +
                             buttonName +
                '            <input type="file" name="' + name + '" class="js-s3_fileupload" ' + multiple + '>' +
                '        </span>' +
                '        <span class="btn btn-danger js-cancel_button btn-sm" style="display:none;">' +
                '            <i class="fa fa-spin fa-spinner"></i>' +
                '            Cancel' +
                '        </span>' +
                '    </div>' +
                '    <div class="col-sm-8">' +
                '        <div class="js-fileupload-progress fileupload-progress" style="display:none;">' +
                '            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">' +
                '                <div class="progress-bar progress-bar-success" style="width:0%;"></div>' +
                '            </div>';
            if(showExtendedBool) {
                template += '<div class="progress-extended">&nbsp;</div>';
            }
            template += '' +
                '        </div>' +
                '    </div>' +
                '</div>';
            return template;
        }
    };

    /**
     *
     * @param options
     * @returns {*}
     */
    $.fn.s3uploader = function(options){
        return this.each(function(){
            // console.log('new uploader init');
            return new S3Uploader(this, options);
        });
    };
    $.fn.s3uploader.defaults = {
        // required, can use $.fn.s3uploader.setDefaults({});
        s3_key_url: '',
        s3_bucket: '',
        s3_acl: 'public_read',
        // optional
        s3_key_payload: {},
        fileupload_options: {},
        multiple: false,
        debug: false,
        optional_s3_folder: '',
        video_tags: [],
        extended_upload_info: true,
        button_name: '',
        drop_zone: '',
        templateEl : null,
        maxChunkSize: 10000000,
        on_file_add: function (element, data) {},
        on_file_saved: function ($element, data) {},
        on_s3_upload: function (data) {}
    };
    $.fn.s3uploader.setDefaults = function(options){
        $.each(options, function(key, val){
            $.fn.s3uploader.defaults[ key ] = val;
        });
    };

})(jQuery, window, document);