<template>
    <div>
        <spinny v-if="actions.fetching" class="block center text-center center-block"></spinny>
        <div class="list box-shadow-z0 white">
                <div
                        v-for="claim in claims"
                        :key="claim.id"
                        :data-claim-id="claim.uuid"
                        class="list-item b-b">
                    <div class="list-left">
                        <h4 style="color: #aaa; margin-top: 10px;" class="cursor m-0 m-b-0 text-muted">
                            <i class="fa fa-square-o" aria-hidden="true"></i>
                        </h4>
                    </div>
                    <div class="list-left">
                        <div class="avatar-thumbnail-container">
                            <div class="avatar-thumbnail _56">
                                <img :src="claim.listable_cover_photo_location">
                            </div>
                        </div>
                    </div>
                    <div class="list-body clearfix" style="margin-left: 126px;">
                        <span class="_500">{{ claim.full_name }}</span>
                        <span class="text-sm text-muted block">Address: {{ claim.shipping_address }}</span>
                        <span class="text-sm text-muted block">Email: <a class="text-primary">{{ claim.email }}</a></span>
                        <span class="text-sm text-muted block">Item: <a class="text-primary">{{ claim.name_alt }} {{ claim.listable_price }}</a></span>
                        <!--<span class="text-sm text-muted block">Sale: <a class="text-primary">{{ claim.sale_name }}</a></span>-->
                        <span class="text-sm text-muted block">Date: <timeago :timestamp="claim.claim_created_at.date"></timeago></span>
                        <!--<span class="text-sm block">-->
                        <!--<multiselect-->
                                <!--:id="claim.id"-->
                                <!--v-model="value"-->
                                <!--@tag="addTag"-->
                                <!--:options="options"-->
                                <!--:taggable="true"-->
                                <!--:multiple="true"-->
                                <!--:close-on-select="false"-->
                                <!--:clear-on-select="false"-->
                                <!--:hide-selected="true"-->
                                <!--placeholder="Pick some"-->
                                <!--label="name"-->
                                <!--track-by="name"></multiselect>-->
                            <!--</span>-->
                    </div>
                </div>
        </div>
    </div>
</template>
<style src="../multiselect/vue-multiselect.min.css"></style>
<script>
    import Spinny from  '../Spinner.vue';
    import Multiselect from 'vue-multiselect';
    import Timeago from "./../Timestamp.vue";
    export default{
        props: {
            fetch_endpoint: {
                required: true,
                type: String
            }
        },
        data: function () {
            return {
                value: [],
                claims: [],
                pagination: {},
                actions: {
                    fetching: true,
                },
                options: [
                    { name: 'Invoiced' },
                    { name: 'Paid' },
                    { name: 'Free' },
                ]
            }
        },
        created(){
            this.fetchClaims();
        },
        methods: {
            addTag (newTag) {
                const tag = {
                    name: newTag,
                };
                this.options.push(tag);
                this.value.push(tag);
            },
            fetchClaims(){
                this.actions.fetching = true;
                this.$http.get(this.fetch_endpoint).then((response) => {
                    this.claims = response.body.data;
                    this.makePagination(response.body.meta.pagination);
                }).finally(()=>{
                    this.actions.fetching = false;
                });
            },
            makePagination(object){

            }
        },
        components: {
            'timeago': Timeago,
            'spinny' : Spinny,
            Multiselect
        }
    }
</script>