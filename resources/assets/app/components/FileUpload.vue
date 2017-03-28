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
        <div :id="imageEl" :class="outer_class">
            <div style="margin-right: 3px" class="upload-template">
                <span
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
                </span>
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
    import currentUser from './current-user';
    export default{
        props : {
            user_hash: {
                type: String,
                default: currentUser() ? currentUser().public_hash : false,
            },
            s3_bucket : {
                required: true
            },
            s3_acl : {
                required: true
            },
            s3_key_url : {
                required: true
            },
            btnClassSize: {
                default: ' btn-sm '
            },
            button_title: {
                default: 'Add images'
            },
            acceptRegEx: {
                default: 'image/*'
            },
            multiple: {
                default: true
            },
            outer_class: {
                default: null
            },
            // Set this to a unique string and when the image is uploaded,
            // the broadcast key will have this appended to it.
            // Example, if ukey == 983j : $Bus.$emit('image:uploaded:983j')
            // and you can now listen for image:uploaded:983j
            ukey: {
                type: String,
                default: null
            }
        },
        data: function() {
            return {
                image: {size: '', key: '', location : '', bucket: ''},
                imageEl : null,
            }
        },
        computed : {
            has_multiple: function() {
                return (this.multiple === true || this.multiple === 'true');
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
                return Math.random().toString(36).replace(/[^a-z]+/g, '').slice(2);
            },
            attachPluginToEl : function(el){
                var that = this;
                $(function(){
                    el.s3uploader({
                        debug: true,
                        save_file_model: false,
                        multiple: that.has_multiple,
                        s3_bucket: that.s3_bucket,
                        s3_acl: that.s3_acl,
                        s3_key_url: that.s3_key_url,
                        s3_key_payload: {
                            user: that.user_hash
                        },
                        templateEl: el.find('.upload-template'),
                        fileupload_options: {},
                        on_s3_upload: function (data, textStatus, jqXHR) {
//                            if ($.inArray(jqXHR.status, [201, 200]) == -1) {
//                                alert('An error occurred with a file, please try that file again.');
//                                return false;
//                            }
                            var xml = $(data);
                            var responseData = {
                                id: data.Key,
                                bucket: data.Bucket,
                                bucket_name: data.Bucket,
                                key: data.Key,
                                location: data.Location
                            };

                            responseData.json = JSON.stringify(responseData);

                            let key = 'image:uploaded';
                            if (that.ukey) {
                                key = key+':'+that.ukey;
                            }

                            $Bus.$emit(key, el, responseData);
                        },
                        on_file_add: function (element, data) {
                            console.log(data.files+' from .vue');
//                            alert(JSON.stringify(data.files, null, 4));
//                            if (data.files[0].type.indexOf("image") == -1) {
//                                alert('File must be an image.');
//                                return false;
//                            }
                            $Bus.$emit('image.added', data.files[0]);
                            return true;
                        }
                    });
                });
            }
        }
    }
</script>