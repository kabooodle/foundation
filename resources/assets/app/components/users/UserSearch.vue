<template>
    <div class="form-group row">
        <label class="control-label col-sm-3">Search User</label>
        <div class="col-sm-9">
            <multiselect
                    v-model="recipient"
                    id="ajax"
                    label="full_name"
                    track-by="id"
                    placeholder=""
                    :custom-label="nameWithUsername"
                    :options="recipients"
                    :multiple="false"
                    :searchable="true"
                    :loading="isLoading"
                    :internal-search="false"
                    :clear-on-select="true"
                    :close-on-select="true"
                    :options-limit="10"
                    :limit="10"
                    @search-change="searchIt">
                <template slot="noResult" v-show="!isLoading">
                    <span class="" v-show="!isLoading" >No results found</span>
                </template>
                <template slot="option" scope="props">
                    <div class="option__desc">
                        <span class="option__title">{{ props.option.full_name }}</span>
                        <small class="option__small text-muted text-small">({{ props.option.username }})</small>
                    </div>
                </template>
            </multiselect>
        </div>
    </div>
</template>
<style src="./../multiselect/vue-multiselect.min.css"></style>
<script>
    import Multiselect from 'vue-multiselect';

    export default{
        props:{
            search_endpoint: {
              type: String
            },
        },
        data(){
            return{
                previousRequest:null,
                isLoading: false,
                recipient: null,
                display_success: false,
                sending: false,
                subject: null,
                message: null,
                recipients:[],
            }
        },
        watch: {
            recipient: function(newUser){
                this.$parent.userClaimer = this.recipient
            }
        },

        methods:{
            searchIt(query){
                this.isLoading = true;
                this.$http.post(this.search_endpoint, {q: query},  {
                    before(request) {
                        if (this.previousRequest) {
                            this.previousRequest.abort();
                        }
                        this.previousRequest = request;
                    }
                }).then((response) => {
                    this.recipients = response.body.data.data;
                    this.isLoading = false;
                });
            },

            nameWithUsername ({ full_name, username }) {
                return `${full_name} (${username})`;
            },
        },

        components: {
            'multiselect' : Multiselect
        }
    }
</script>
