<template>
    <table class="vuetable table table-condensed table-as-list white tablesaw tablesaw-stack">
        <thead>
        <tr>
            <th></th>
            <th>Claimer</th>
            <th>Item</th>
            <th>Listing</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <tr
                v-for="claim in claims"
                :key="claim.id"
                :data-claim-id="claim.uuid">
            <td>
                <input type="checkbox">
            </td>
            <td>
                <div class="">{{ claim.full_name }}</div>
                <div>{{ claim.email }}</div>
                <div>{{ claim.shipping_address }}</div>
            </td>
            <td>
                <div class="avatar-thumbnail-container">
                    <div class="avatar-thumbnail _26 m-r-xs">
                        <img :src="claim.listable_cover_photo_location">
                    </div>
                    <span>{{ claim.name_alt }}</span>
                </div>
            </td>
            <td>
                {{ claim.sale_name }}
            </td>
            <td>
                <multiselect
                        v-model="value"
                        :options="options"
                        :multiple="true"
                        :close-on-select="false"
                        :clear-on-select="false"
                        :hide-selected="true"
                        placeholder="Pick some" label="name" track-by="name"></multiselect>
            </td>
            <!--<td>-->
            <!--<timeago-->
            <!--:timestamp="claim.claim_created_at"-->
            <!--&gt;</timeago>-->
            <!--<small class="block text-xs text-muted">{{ claim.claim_created_at }}</small>-->
            <!--</td>-->
        </tr>
        </tbody>
    </table>
</template>
<style src="../multiselect/vue-multiselect.min.css"></style>
<script>
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
            fetchClaims(){
                this.$http.get(this.fetch_endpoint).then((response) => {
                    this.claims = response.body.data;
                    this.makePagination(response.body.meta.pagination);
                });
            },
            makePagination(object){

            }
        },
        components: {
            'timeago': Timeago,
            Multiselect
        }
    }
</script>