<template>
    <div>
        <ul class="pagination" v-if="pagination.last_page > 0" :class="sizeClass">
            <li  :class="{ 'disabled' : pagination.current_page == 1 }">
                <a :class="{ 'disabled' : pagination.current_page == 1 }" class="btn white btn-xs" href="#" :aria-label="config.ariaPrevioius" @click.prevent="changePage(1)">
                    <span aria-hidden="true" v-html="config.firstText"></span>
                </a>
            </li>

            <li  :class="{ 'disabled' : pagination.current_page <= 1 }">
                <a class="btn white btn-xs" href="#" :aria-label="config.ariaPrevioius" @click.prevent="changePage(pagination.current_page - 1)">
                    <span aria-hidden="true" v-html="config.previousText"></span>
                </a>
            </li>

            <li v-for="num in array" :class="{ 'active': num === pagination.current_page }">
                <a class="btn white btn-xs" href="#" @click.prevent="changePage(num)">{{ num }}</a>
            </li>

            <li  :class="{ 'disabled' : pagination.current_page === pagination.last_page || pagination.last_page === 0 }">
                <a class="btn white btn-xs" href="#" v-if="pagination.current_page < pagination.last_page" :aria-label="config.ariaNext" @click.prevent="changePage(pagination.current_page + 1)">
                    <span aria-hidden="true" v-html="config.nextText"></span>
                </a>
            </li>

            <li :class="{ 'disabled' : pagination.current_page == pagination.last_page }">
                <a :class="{ 'disabled' : pagination.current_page == pagination.last_page }" class="btn white btn-xs" href="#" :aria-label="config.ariaPrevioius" @click.prevent="changePage(pagination.last_page)">
                    <span aria-hidden="true" v-html="config.lastText"></span>
                </a>
            </li>
        </ul>
    </div>
</template>
<script>
    export default{
        props: {
            pagination: {
                type: Object,
                required: true
            },
            callback: {
                type: Function,
                required: true
            },
            options: {
                type: Object
            },
            size: {
                type: String
            }
        },
        computed: {
            array () {
                if (this.pagination.last_page <= 0) {
                    return [];
                }

                let from = this.pagination.current_page - this.config.offset;
                if (from < 1) {
                    from = 1;
                }

                let to = from + (this.config.offset * 2);
                if (to >= this.pagination.last_page) {
                    to = this.pagination.last_page;
                }

                let arr = [];
                while (from <=to) {
                    arr.push(from);
                    from++;
                }

                return arr;
            },
            config () {
                return Object.assign({
                    offset: 2,
                    ariaPrevious: 'Previous',
                    ariaNext: 'Next',
                    firstText: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>',
                    previousText: '<i class="fa fa-angle-left" aria-hidden="true"></i>',
                    nextText: '<i class="fa fa-angle-right" aria-hidden="true"></i>',
                    lastText: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    alwaysShowPrevNext: false
                }, this.options);
            },
            sizeClass () {
                if (this.size === 'large') {
                    return 'pagination-lg';
                } else if(this.size === 'small') {
                    return 'pagination-sm';
                } else {
                    return '';
                }
            }
        },
        watch: {
            'pagination.per_page' (newVal, oldVal) {
                if (+newVal !== +oldVal) {
                    this.callback();
                }
            }
        },
        methods: {
            showPrevious () {
                return this.config.alwaysShowPrevNext || this.pagination.current_page > 1;
            },
            showNext () {
                return this.config.alwaysShowPrevNext || this.pagination.current_page < this.pagination.last_page;
            },
            changePage (page) {
                if (this.pagination.current_page === page) {
                    return;
                }

                this.$set(this.pagination, 'current_page', page);
                this.callback();
            }
        }
    }
</script>


