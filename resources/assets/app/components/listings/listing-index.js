import VCard from '../users/V-Card.vue';
import ListingCards from './ListingCards.vue';
import Spinny from '../Spinner.vue';

new Vue({
    el: '#listing-page',
    data: {
        styles: [],
        sizes: [],
        filtering: false,
        filtered: null
    },
    mounted(){
        $Bus.$on('listing-filter:completed', (items)=>{
            this.filtered = items.length;
            this.filtering = false;
        });
    },
    computed: {
        has_filters(){
            return (this.styles.length > 0 || this.sizes.length > 0);
        },
    },
    methods: {
        toggleStyleListItem(styleId, event){
            let $el = $(event.target).closest('.style-list-item');
            if ($el.hasClass('active')) {
                $el.removeClass('active');
                let index = this.styles.indexOf(styleId);
                if (index >= 0) {
                    this.styles.splice(index, 1);
                }
            } else {
                $el.addClass('active');
                this.styles.push(styleId);
            }
        },
        toggleSizeListItem(sizeId, styleId, event){
            let $el = $(event.target).closest('.size-list-item');
            sizeId = parseInt(sizeId);
            styleId = parseInt(styleId);

            if ($el.hasClass('active')) {
                $el.removeClass('active');
                $el.find('.nav-text').find('i.selected-icon').remove();
                let index = this.sizes.indexOf(sizeId);
                if (index >= 0) {
                    this.sizes.splice(index, 1);
                }
            } else {
                $el.addClass('active');
                $el.find('.nav-text').html($el.find('.nav-text').html()+' <i class="fa selected-icon fa-check-circle" aria-hidden="true"></i>');
                this.sizes.push(sizeId);
            }
        },
        filterListing(){
            this.filtering = true;
            $Bus.$emit('listing-filter:request', this.styles, this.sizes);
        }
    },
    components: {
        'listing-cards' : ListingCards,
        'v-card' : VCard,
        'spinny' : Spinny
    },
})