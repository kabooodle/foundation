<template>
<div v-for="comment in comments">
<a>hi</a>
</div>
</template>

<script>
 export default {
            name : 'commentable',
              data () {
                return {
                    comments: [{'events':2}, {'events':2}]
                }
            },
          methods: {
              increment: function () {
                  this.counter += 1;
                  this.$emit('increment');
              }
          }
 };
 </script>