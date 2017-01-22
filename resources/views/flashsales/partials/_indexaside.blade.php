<div class="navbar-side p-a " id="navbarSide">
    <div class="box ">
        <div class="box-body clearfix">
                <div class="form-group row">
                    <label class=" form-control-label col-sm-4 text-sm">Sale name</label>
                    <div class="col-sm-8">
                        <input type="text" name="name" v-model="search.sale_name" class="form-control">
                    </div>
                </div>
                {{--<div class="form-group row">--}}
                    {{--<label class=" form-control-label col-sm-4 text-sm">Seller name(s)</label>--}}
                    {{--<div class="col-sm-8">--}}
                        {{--<multiselect--}}
                                {{--id="admins_el"--}}
                                {{--label="username"--}}
                                {{--track-by="id"--}}
                                {{--placeholder=""--}}
                                {{--:options="search.sellers_query"--}}
                                {{--:multiple="true"--}}
                                {{--:searchable="true"--}}
                                {{--:loading="search.searching"--}}
                                {{--:internal-search="false"--}}
                                {{--:clear-on-select="true"--}}
                                {{--:close-on-select="true"--}}
                                {{--:options-limit="10"--}}
                                {{--:limit="10"--}}
                                {{--v-model="search.sellers"--}}
                        {{--@search-change="searchSellers">--}}
                        {{--<template slot="option" scope="props">--}}
                            {{--<div class="option__desc">--}}
                                {{--<span class="option__title">@{{ props.option.username }} (@{{ props.option.full_name}})</span>--}}
                            {{--</div>--}}
                        {{--</template>--}}
                        {{--</multiselect>--}}
                    {{--</div>--}}
                {{--</div>--}}
                <div class="form-group row p-b-0 m-b-0">
                    <div class="col-sm-9 col-sm-offset-3">
                        <button type="button" @click.prevent="performSearch" class="btn-sm btn primary">Go</button>
                        <button type="button" @click.prevent="resetSearch" class="btn white btn-sm btn-toggle-reset" >Reset</button>
                        <a  href="#" class=" m-l-sm text-sm btn-toggle-filters" >Close</a>
                    </div>
                </div>
        </div>
    </div>
</div>