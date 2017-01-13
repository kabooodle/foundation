<template>
    <div>
        <div class="inventory-overlay" style="display: none;"></div>
        <div class="pop-out-overlay" style="display: none;">
            <a href="javascript:;" class="close-overlay" v-on:click="closeOverlay" aria-label="close">
                <img :src="img_url" alt="Icon shot x light">
            </a>
            <div class="overlay-content group">
            </div>
        </div>
    </div>
</template>
<script>
    export default{
        data: function() {
            return {
                promptOnClose : true,
                defaultContent: '<div class="text-center center-block" ' +
                'style="position: absolute; top: 50%; margin-top: -20px; margin-left: -20px; left: 50%; ">' +
                '<img src="'+KABOOODLE_APP.makeStaticAsset("assets/images/icons/ring-alt.gif")+'"></div>'
            }
        },
        computed : {
            img_url(){
                return KABOOODLE_APP.makeStaticAsset("assets/images/icons/over-close-white.png");
            },
        },
        mounted: function() {
            this.resetOverlay();
        },
        created : function() {
            var scope = this;

            $Bus.$on('popout-overlay:change-prompt',  function(promptOnClose){
                scope.setPromptOnClose(promptOnClose);
            });
            $Bus.$on('popout-overlay:close',  function(){
                scope.closeOverlay();
            });
            $Bus.$on('popout-overlay:request-open', function(content) {
                scope.openOverlay(content);
            });
            $Bus.$on('popout-overlay:change-content', function(content, promptOnClose) {
                scope.changeOverlayContent(content,promptOnClose);
            });
        },
        methods : {
            setPromptOnClose : function(promptOnClose) {
                this.promptOnClose = promptOnClose;
            },
            openOverlay : function(content){
                $('body').addClass('noscroll');
                $('.inventory-overlay').show();
                $('.pop-out-overlay').show();
                if(content) {
                    this.changeOverlayContent(content);
                }
                this.$emit('popout-overlay:opened');
            },
            resetOverlay : function() {
                $('body').removeClass('noscroll');
                $('.inventory-overlay').hide(200);
                $('.pop-out-overlay').hide(200);
                this.changeOverlayContent(this.defaultContent, true);
                $Bus.$emit('popout-overlay:closed');
            },
            closeOverlay : function() {
                var scope = this;
                setTimeout(function(){
                    if (this.promptOnClose) {
                        confirmModal(function(noty){
                            scope.resetOverlay();
                            noty.close();
                        });
                    } else {
                        scope.resetOverlay();
                    }
                }, 400)
            },
            changeOverlayContent : function(content, promptOnClose) {
                // Because this method may be called immediately after a prompt change was called,
                // lets ignore undefined promptOnClose so that we don't inadvertently override something
                // previously set.  If we want it set, we would pass it. simple.
                if (typeof promptOnClose !== 'undefined') {
                    this.setPromptOnClose(promptOnClose);
                }
                $('.pop-out-overlay').find('.overlay-content').html(content);
            }
        }
    }
</script>
