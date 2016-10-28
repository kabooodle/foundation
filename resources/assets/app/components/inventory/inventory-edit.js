


new Vue({
    el: '#inventory_manage',
//     data: {
//         styles: {!! $styles->toJson() !!},
// style: {!! $item->style->toJson() !!},
// sizes: {!! $item->style->sizes->toJson() !!},
// item: {!!  $item->toJson()  !!},
// images: {!! $item->files->toJson() !!}
// },
    ready: function () {
        console.log('Inventory management ready');
        $('.selectized').selectize({
            delimiter: ',',
            persist: false,
            valueField: 'tag',
            labelField: 'tag',
            searchField: 'tag',
            plugins: ['remove_button'],
            create: function (input) {
                return {
                    tag: input
                }
            }
        });
    },
    created : function() {
        const scope = this;

        $Bus.$on('image:uploaded', function(el, image){
            scope.insertImage(image);
        });
    },
    methods: {
        getStyleById: function(id) {
            return $.findFirst(this.styles, function(obj) {
                return obj.id == id;
            });
        },
        setSizes : function(sizes){
            this.sizes = sizes;
        },
        setStyle : function(style){
            this.style = style;
        },
        changeStyle: function(e){
            var $el = $(e.target),
                id = parseInt($el.val()),
                style = this.getStyleById(id);

            this.setStyle(style);
            this.setSizes(style.sizes);
        },
        validateForm: function (e) {
            var scope = this;
                // $that = $(e.target);

            if (scope.images.length == 0) {
                e.preventDefault();
                alert('Must have at least 1 image');
                return false;
            }

//                this.$validate(true, function () {
//                    if (scope.$inventory_validation.invalid || scope.images.length == 0) {
//                        e.preventDefault();
//                        if (scope.images.length == 0) {
//                            alert('Must have at least 1 image');
//                        }
//                        return false;
//                    }
//                });
        },
        deleteImage: function(image){
            let index = this.images.indexOf(image);
            this.images.splice(index,1);
        },
        insertImage: function(image) {
            this.images.unshift(image);
        }
    },
    watch : {
        sizes : function(v){
            var formSizeEl = $('#form_size_el');
            formSizeEl.empty();
            $.each(v, function(i,value){
                formSizeEl.append($('<option>').text(value.name).prop('value', value.id));
            });
        }
    }
});