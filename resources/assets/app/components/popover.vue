<template>
    <div>
        <div class="pop-out-overlay shot-overlay" style="display: none;">
            <a href="javascript:;" class="close-overlay" v-on:click="closeOverlay" aria-label="close">
                <img src="https://d13yacurqjgara.cloudfront.net/assets/icon-shot-x-light-40c073cd65443c99d4ac129b69bf578c8cf97d69b78990c00c4f8c5873b0d601.png" alt="Icon shot x light">
            </a>
            <div class="overlay-content group">
            </div>
        </div>
    </div>
</template>
<style>
    .noscroll {
        overflow-x: hidden;
        overflow-y: hidden;
    }

    .shot-overlay {
        background: rgba(30,30,30,0.9);
        top: 0;
        left: 0;
        z-index: 9997;
        width: 100%;
        height: 100%;
        position: fixed;
        overflow-y: auto;
        box-sizing: border-box;
        -webkit-overflow-scrolling: touch;
    }
    a.close-overlay {
        position: absolute;
        top: 0;
        right: 0;
        width: 20px;
        height: 20px;
        padding: 20px;
        opacity: .5;
        z-index: 9998;
        background-repeat: no-repeat;
        background-position: 20px 20px;
        background-image: url(https://dribbble.com/assets/icon-shot-x-light-40c073cd65443c99d4ac129b69bf578c8cf97d69b78990c00c4f8c5873b0d601.png);
    }
    .overlay-content {
        position: absolute;
        z-index: 9997;
        top: 20px;
        left: 50%;
        width: 920px;
        min-height: 400px;
        margin-left: -460px;
        padding: 40px 40px;
        background: #f4f4f4;
        background-position: fixed;
        box-sizing: border-box;
        border-radius: 6px;
    }
    a.close-overlay img {
        height: 0;
    }
    @media only screen and (max-width: 959px) {
        .shot-overlay {
            overflow-y: auto;
        }
        a.close-overlay {
            opacity: .34;
            background-image: url(/assets/icon-shot-x-7dbc9cdd6856806bcc277a21513ac00fd402944f9046212dfe5151e1bb3a5ab8.png);
        }
        .overlay-content {
            left: auto;
            top: 0;
            width: 100%;
            max-width: 100%;
            margin-left: 0;
            padding: 20px;
            border-radius: 0;
        }
    }
</style>
<script>
    export default{
        data: function() {
            return {
                promptOnClose : true,
                defaultContent: '<div class="text-center center-block" style="position: absolute; top: 50%; margin-top: -20px; margin-left: -20px; left: 50%; "><i class="fa fa-2x fa-spin fa-spinner"></i></div>'
            }
        },
        created : function() {
            var scope = this;

            this.resetOverlay();

            // Register event listeners here due to the need of Bus
            $Bus.$on('popout-overlay:close',  function(){
                scope.closeOverlay();
            });
            $Bus.$on('popout-overlay:request-open', function(content) {
                scope.openOverlay(content);
            });
            $Bus.$on('popout-overlay:change-content', function(content) {
                scope.changeOverlayContent(content);
            });
        },
        methods : {
            openOverlay : function(content){
                $('body').addClass('noscroll');
                $('.shot-overlay').show();
                if(content) {
                    this.changeOverlayContent(content);
                }
                this.$emit('popout-overlay:opened');
            },
            resetOverlay : function() {
                $('body').removeClass('noscroll');
                $('.shot-overlay').hide();
                this.changeOverlayContent(this.defaultContent);
                this.$emit('popout-overlay:closed');
            },
            closeOverlay : function() {
                var scope = this;
                if (this.promptOnClose) {
                    confirmModal(function(noty){
                        scope.resetOverlay();
                        noty.close();
                    });
                } else {
                    scope.resetOverlay();
                }
            },
            changeOverlayContent : function(content) {
                $('.shot-overlay').find('.overlay-content').html(content);
            }
        }
    }
</script>
