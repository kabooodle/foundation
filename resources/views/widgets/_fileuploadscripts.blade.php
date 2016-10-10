@push('header-styles')
<link rel="stylesheet" href="https://blueimp.github.io/jQuery-File-Upload/css/jquery.fileupload.css" type="text/css" />
<link rel="stylesheet" href="https://blueimp.github.io/jQuery-File-Upload/css/jquery.fileupload-ui.css" type="text/css"/>
@endpush

<script>
    Vue.component('image-attach', {
        props : [
            {
                name: 'btnClassSize',
                'default': ' btn-sm '
            },
            {
                name: 'buttonTitle',
                'default': 'Add Images'
            },
            {
                name: 'acceptRegEx',
                'default': 'image/*'
            },
            {
                name: 'multiple',
                'default' : true
            }
        ],
        data: function() {
            return {
                multiple: true,
                image: {size: '', key: '', location : '', bucket: ''},
                imageEl : null
            }
        },
        computed : {
            has_multiple: function() {
                return this.multiple == true ? 'multiple' : null;
            }
        },
        ready : function() {
            console.log('File component ready.');
            this.imageEl = this.buildImageEl();
        },
        watch : {
            imageEl : function(v){
                this.attachPluginToEl($('#'+v));
            }
        },
        template: '' +
        '<div id="@{{ imageEl }}">' +
        '<div style="margin-right: 3px" class="upload-template"> ' +
        '<button type="button" class="btn white @{{ btnClassSize }} fileinput-button" style="display: inline-block;">' +
        '@{{ buttonTitle }}<input type="file" name="file" class="js-s3_fileupload" accept="@{{ acceptRegEx }}"  multiple /> ' +
        '</button> ' +
        '<button type="button" class="btn danger @{{ btnClassSize }} js-cancel_button" style="display: none;">' +
        'Cancel ' +
        '<div class="js-fileupload-progress fileupload-progress m-b-0 p-b-0" style="display: none;  margin-left: -9px; margin-right: -9px;"> ' +
        '<div style="height: 8px;" class="progress progress-striped active m-b-0 p-b-0" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="5"> ' +
        '<div class="progress-bar progress-bar-success" style="width: 5%;"></div> ' +
        '</div> ' +
        '</div> ' +
        '</button> ' +
        '</div>' +
        '</div>',
        methods : {
            buildImageEl : function(){
                return Math.random().toString(36).slice(2);
            },
            attachPluginToEl : function(el){
                var that = this;
                setTimeout(function() {
                    el.s3uploader({
                        save_file_model: false,
                        multiple: true,
                        s3_bucket: 'kabooodle-storage',
                        s3_key_url: '{{ route('api.files.sign') }}',
                        s3_key_payload: {
                            user: '{{ user()->public_hash }}'
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
                                key: xml.find('Key').text(),
                                location: xml.find('Location').text()
                            };

                            responseData.json = JSON.stringify(responseData);

                            that.$dispatch('image:uploaded', el, responseData);
                        },
                        on_file_add: function (element, data) {
                            if (data.files[0].type.indexOf("image") == -1) {
                                alert('File must be an image.', false);
                                return false;
                            }
                            return true;
                        }
                    });
                },100);
            }
        }
    });
</script>

@push('footer-scripts')
<script src="/assets/js/fileupload/js/vendor/jquery.ui.widget.js"></script>
<script src="https://blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script>
<script src="//blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
<script src="/assets/js/fileupload/js/jquery.iframe-transport.js"></script>
<script src="/assets/js/fileupload/js/jquery.fileupload.js"></script>
<script src="/assets/js/fileupload/js/jquery.fileupload-process.js"></script>
<script src="/assets/js/fileupload/js/jquery.fileupload-image.js"></script>
<script src="/assets/js/fileupload/js/jquery.fileupload-ui.js"></script>
@endpush

