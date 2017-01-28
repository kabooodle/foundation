<template>
    <div class="onboard-card onboard_wrapper">
        <div class="onboard-body text-center">
            <h1 class="onboard-card-title">
                <slot name="title"></slot>
            </h1>
            <h2 class="onboard-card-sub-title text-center m-b-3">
               <slot name="subtext"></slot>
            </h2>
            <slot name="extra"></slot>
        </div>
    </div>
</template>
<script>
    export default {}
</script>
