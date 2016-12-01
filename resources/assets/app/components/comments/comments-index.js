import Comments from './comments-index.vue';

new Vue({
    el: '#comments-index',
    ready: function(){
        console.log('Commentable ready.');
    },
    components: {
        'comments-index' : Comments
    }
});