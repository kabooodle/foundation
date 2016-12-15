<template>
    <div v-if="kb_overlay_show === true">
        <div class="kb_overlay">
            <div class="kb_overlay__inner">
                <img :src="img_url">
            </div>
        </div>
    </div>
</template>
<style>
    .kb_overlay {
        background: rgba(255,255,255,.98);
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
    .kb_overlay__inner {
        position: fixed;
        background: #fff;
        padding: 3px;
        border-radius: 30px;
        top: 50%;
        left: 50%;
        margin-left: -16px;
        margin-top: -16px;
    }
</style>
<script>
    export default{
        data: function() {
            return {
                kb_overlay_show : false,
            }
        },
        computed : {
            img_url(){
              return KABOOODLE_APP.makeStaticAsset("assets/images/icons/ring-alt.gif");
            },
        },
        created : function() {
            const scope = this;

            $Bus.$on('loader:request-close', function(){
                scope.kb_overlay_show = false;
            });

            $Bus.$on('loader:request-show', function(){
                scope.kb_overlay_show = true;
            });
        }
    }
</script>
