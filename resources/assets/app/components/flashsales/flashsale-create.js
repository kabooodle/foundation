import BuildFlashsale from './Build-Flashsale.vue';

new Vue({
    el: '#create_flashsale',
    data: {},
    methods: {
        buildGroup(){
            $Bus.$emit('build-group:click');
        },
    },
    components: {
        'build-flashsale': BuildFlashsale,
    }
});