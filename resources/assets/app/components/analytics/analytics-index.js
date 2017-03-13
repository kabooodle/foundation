/*
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

require('d3');
window.c3 = require('c3');
require('keen-js');

const chartTypes = [
    'areachart',
    'barchart',
    'columnchart',
    'linechart',
    'metric',
    'piechart',
    'table'
];

new Vue({
    el: '#analytics_index',
    data: {
        keenConfig : KCONFIG,
        keenClient: null,
    },
    mounted(){
        this.keenClient = new Keen({
            projectId: this.keenConfig.project_id,
            readKey: this.keenConfig.read_key
        });

        Keen.ready(()=>{

            var query = new Keen.Query("sum", {
                event_collection: "accepted_claim",
                target_property: "price",
                timeframe: "this_1_years",
                timezone: "UTC"
            });

            this.keenClient.draw(query, document.getElementById("sum_dollars"), {
                title: 'Total Dollars',
                chartOptions: {
                    prefix: '$',
                }
            });


            var query = new Keen.Query("count", {
                event_collection: "accepted_claim",
                timeframe: "this_1_years",
                timezone: "UTC"
            });

            this.keenClient.draw(query, document.getElementById("sum_total"), {
                title: 'Total Sales',
            });


            var query = new Keen.Query("count", {
                event_collection: "accepted_claim",
                group_by: [
                    "listable.name_alt"
                ],
                interval: "daily",
                timeframe: "this_14_days",
                timezone: "UTC"
            });

            this.keenClient.draw(query, document.getElementById("my_chart"), {
                library: 'c3',
                chartType: 'area',
                title: 'Daily Total',
                chartOptions: {
                    legend: {
                        position: 'right'
                    },
                    axis: {
                        y: {
                            tick: {
                                format: function (d) {
                                    return (parseInt(d) == d) ? d : null;
                                }
                            }
                        },
                    }
                }
            });
        });
    },
    methods: {
        getKeenClient(){
            return this.keenClient;
        },
    }
});