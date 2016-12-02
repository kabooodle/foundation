<script>
    export default{
        props: {
            route : {
                type: String,
                required: true
            },
            resource_hash : {
                type: String,
                required: true
            }
        },
        render : function(){
            this.trackView();
        },
        methods: {
            trackView : function(){
                this.$http.post(this.route, {resource: this.resource_hash}).then(function(response){

                        },function(response){
                            throw new Error(response);
                        }).catch(function(){});
            },
        }
    }
</script>
