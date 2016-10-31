<style>
    .fileinput-button {
        position: relative;
        overflow: hidden;
        display: inline-block;
    }
    .fileinput-button input {
        position: absolute;
        top: 0;
        right: 0;
        margin: 0;
        opacity: 0;
        -ms-filter: 'alpha(opacity=0)';
        font-size: 200px !important;
        direction: ltr;
        cursor: pointer;
    }

    /* Fixes for IE < 8 */
    @media screen\9 {
        .fileinput-button input {
            filter: alpha(opacity=0);
            font-size: 100%;
            height: 100%;
        }
    }


    .fileupload-buttonbar .btn,
    .fileupload-buttonbar .toggle {
        margin-bottom: 5px;
    }
    .progress-animated .progress-bar,
    .progress-animated .bar {
        background: url("../img/progressbar.gif") !important;
        filter: none;
    }
    .fileupload-process {
        float: right;
        display: none;
    }
    .fileupload-processing .fileupload-process,
    .files .processing .preview {
        display: block;
        width: 32px;
        height: 32px;
        background: url("../img/loading.gif") center no-repeat;
        background-size: contain;
    }
    .files audio,
    .files video {
        max-width: 300px;
    }

    @media (max-width: 767px) {
        .fileupload-buttonbar .toggle,
        .files .toggle,
        .files .btn span {
            display: none;
        }
        .files .name {
            width: 80px;
            word-wrap: break-word;
        }
        .files audio,
        .files video {
            max-width: 80px;
        }
        .files img,
        .files canvas {
            max-width: 100%;
        }
    }
</style>
<template>
    <div>
        <div :id="imageEl">
            <div style="margin-right: 3px" class="upload-template">
                <button
                        type="button"
                        :class="'btn white '+btnClassSize+' fileinput-button'" style="display: inline-block;">
                    {{ button_title }}
                    <template v-if="has_multiple">
                    <input
                        type="file"
                        name="file"
                        class="js-s3_fileupload"
                        :accept="acceptRegEx"
                        multiple="multiple"
                    />
                    </template>
                    <template v-else>
                        <input
                                type="file"
                                name="file"
                                class="js-s3_fileupload"
                                :accept="acceptRegEx"
                        />
                    </template>
                </button>
                <button type="button" :class="'btn danger '+btnClassSize+' js-cancel_button'" style="display: none;">
                    Cancel
                    <div class="js-fileupload-progress fileupload-progress m-b-0 p-b-0" style="display: none;  margin-left: -9px; margin-right: -9px;">
                        <div style="height: 8px;" class="progress progress-striped active m-b-0 p-b-0" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="5">
                            <div class="progress-bar progress-bar-success" style="width: 5%;"></div>
                        </div>
                    </div>
                </button>
            </div>
        </div>
    </div>
</template>
<script>
    require('../../vendor/fileupload/js/vendor/jquery.ui.widget');
    require('../../vendor/fileupload/js/vendor/load-image');
    require('../../vendor/fileupload/js/vendor/canvas-to-blob.min');
    require('../../vendor/fileupload/js/jquery.iframe-transport');
    require('../../vendor/fileupload/js/jquery.fileupload');
    require('../../vendor/fileupload/js/jquery.fileupload-process');
    require('../../vendor/fileupload/js/jquery.fileupload-image');
    require('../../vendor/fileupload/js/jquery.fileupload-ui');
    import s3uploader from '../../app/s3uploader';

    export default{
        props : {
            user_hash: {
                required: true
            },
            s3_key_url : {
                required: true
            },
            btnClassSize: {
                default: ' btn-sm '
            },
            button_title: {
                default: 'Add Images'
            },
            acceptRegEx: {
                default: 'image/*'
            },
            multiple: {
                default: true
            }
        },
        data: function() {
            return {
                image: {size: '', key: '', location : '', bucket: ''},
                imageEl : null
            }
        },
        computed : {
            has_multiple: function() {
                return this.multiple == 'true' ? true : false;
            }
        },
        created : function() {
            console.log('File component ready.');
            this.imageEl = this.buildImageEl();
        },
        watch : {
            imageEl : function(v){
                this.attachPluginToEl($('#'+v));
            }
        },
        methods : {
            buildImageEl : function(){
                return Math.random().toString(36).slice(2);
            },
            attachPluginToEl : function(el){
                var that = this;
                $(function(){
                    el.s3uploader({
                        save_file_model: false,
                        multiple: that.has_multiple,
                        s3_bucket: 'kabooodle-storage',
                        s3_key_url: that.s3_key_url,
                        s3_key_payload: {
                            user: that.user_hash
                        },
                        templateEl: el.find('.upload-template'),
                        fileupload_options: {},
                        on_s3_upload: function (data, textStatus, jqXHR) {
                            if ($.inArray(jqXHR.status, [201, 200]) == -1) {
                                alert('An error occurred with a file, please try that file again.');
                                return false;
                            }
                            var xml = $(data);
                            var responseData = {
                                id: xml.find('Key').text(),
                                bucket: xml.find('Bucket').text(),
                                bucket_name: xml.find('Bucket').text(),
                                key: xml.find('Key').text(),
                                location: xml.find('Location').text()
                            };

                            responseData.json = JSON.stringify(responseData);

                            $Bus.$emit('image:uploaded', el, responseData);
                        },
                        on_file_add: function (element, data) {
                            if (data.files[0].type.indexOf("image") == -1) {
                                alert('File must be an image.', false);
                                return false;
                            }
                            return true;
                        }
                    });
                });
            }
        }
    }
</script>