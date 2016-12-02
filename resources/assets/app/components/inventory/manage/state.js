export default {
    inventory_route: null,

    // Array of user inventory items
    inventory_items: [],

    // Array of postable objects (flash sales, facebook groups)
    postables: {
        facebookgroups: [],
        flashsales: []
    },

    selected: {
        // Holds postables that have been selected
        postables: {
            fb_albums: [],
            flashsales: []
        },

        // Holds sizes that have been selected
        sizes: [],

        // Holds items that have been selected
        items: [],

        // Toggle of the selected fB group
        fb_group: null,

        // Toggle of the selected fb album
        fb_album: null
    },

    actions: {
        refreshing_data: false,
        posting_to_sales: false,
        fb_advanced_menu: false
    },

    sums: {
        selected_postables: 0,
        selected_sales_sum: 0,
    }
}