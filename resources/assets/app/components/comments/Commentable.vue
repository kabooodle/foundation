<template>
    <div>
        <div v-if="comments_ready">
            <div class="box">
                <div class="box-header clearfix">
                    <h3 class="pull-left">Comments <span class="label grey-400 text-white" id="comments_count">{{ comments.length }}</span></h3>
                    <!--{{&#45;&#45;<div class="pull-right">&#45;&#45;}}-->
                    <!--{{&#45;&#45;<button id="comment_delete_all_btn" data-model-id="{{  modelId  }}" type="button" class="btn white btn-xs text-muted">Delete All</button>&#45;&#45;}}-->
                    <!--{{&#45;&#45;</div>&#45;&#45;}}-->
                </div>
                <div class="box-body">
                    <div class="streamline b-l m-l-md" id="comments_container">
                        <div v-for="comment in comments">
                            <div
                                    :id="'comment_'+comment.id"
                                    class="sl-item"
                                 :data-uuid="comment.uuid"
                                 :data-id="comment.id"
                                 :data-author-id="comment.author.public_hash"
                                 :data-author="comment.author.name">
                                <div class="sl-left">
                                    <a :href="'/users/'+ comment.author.username" class="avatar_container _32 inline avatar-thumbnail">
                                        <img :src="comment.author.avatar ? comment.author.avatar.location : '/assets/images/logo/roboto-avatar.png'">
                                    </a>
                                </div>
                                <div class="sl-content">
                                    <div class="sl-author">
                                        <a :href="'/users/'+ comment.author.username" class="text-u-l-on-hover _500">{{ comment.author.username }} <span v-if="modelobject.owner.id == comment.author.id" style="font-weight: normal;" class="text-xs text-muted">(owner)</span></a>
                                    </div>
                                    <div v-html="comment.text"></div>
                                    <div class="sl-footer sl-date clearfix">
                                        <ul class="text-muted list-inline pull-left">
                                            <li class="list-inline-item">
                                                <a :href="'#comment_'+comment.id">
                                                    <timestamp :timestamp="comment.created_at"></timestamp>
                                                </a>
                                            </li>
                                            <li class="list-inline-item" v-if="userCanDelete(comment)"><button type="button" class="white btn btn-text btn-xs" @click="deleteComment(comment, $event)">Delete</button></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box m-a-0 b-a">
                        <form id="comment_new_form" method="POST" :action="post_route" accept-charset="UTF-8" @submit="addNewComment">
                            <textarea id="comment_new_text" v-model="newcomment" name="text_raw" data-toggle="emojione" class="form-control no-border" rows="3" placeholder="Type something..."></textarea>
                            <div class="box-footer clearfix">
                                <button id="comment_new_submit_btn" type="submit" class="btn primary pull-right btn-sm" :disabled="!newcomment" >
                                  Post Comment <spinny v-if="posting"></spinny></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="center-block text-center">
                <spinny size="30"></spinny>
            </div>
        </div>
    </div>
</template>
<script>
    import Spinny from '../Spinner.vue';
    import Timestamp from '../Timestamp.vue';
    import currentUser from '../current-user';
    export default{
        props:  ["post_route", "modelobject", "comments_url"],
        data: function () {
            return {
                posting : false,
                comments : [],
                comments_ready: false,
                newcomment: null,
            }
        },
        created(){
            const $scope = this;

            this.$http.get(this.comments_url).then(function(response){
                $scope.comments = response.body.data;
                $scope.comments_ready = true;
                setTimeout(function(){
                    $scope.prepareCommentForm();
                }, 0);
            });
        },
        methods : {
            prepareCommentForm : function(){
                const $scope = this;
                $(function(){
                    $('[data-toggle="emojione"]').emojioneArea({
                        pickerPosition: "bottom",
                        filtersPosition: "bottom",
                        useSprite: false,
                        autocomplete : true,
                        saveEmojisAs: "shortname",
                        events: {
                            keyup: function (editor, event) {
                                $scope.newcomment = this.getText();
                            }
                        }
                    });
                });
            },
            userCanDelete : function(comment){
                if (! currentUser()) {
                    return false;
                }
                return currentUser() && (currentUser().id === this.modelobject.user.id || comment.author.id === currentUser().id)
            },
            addNewComment : function(e){
                e.preventDefault();

                if (currentUser()) {
                    this.newcomment = null;
                    const $scope = this;

                    this.posting = true;
                    this.$http.post(this.post_route, {text_raw : $('#comment_new_text').val()}).then(function (response) {
                        $scope.comments.push($.parseJSON(response.body.data.json));
                        $scope.resetCommentForm();
                    }, function(){
                        notify({'text' : 'An error occurred, please try again.'});
                        $scope.resetCommentForm();
                    }).then(function(){
                        $scope.posting = false;
                    });
                } else {
                    notify({
                        'text': 'You must be signed in to comment',
                        'type': 'information'
                    });
                }
            },
            deleteComment: function(comment, e){
                e.preventDefault();
                if (!this.userCanDelete(comment)) {
                    return false;
                }
                var $el = $(e.target);
                var $scope = this;

                if ($Bus.$emit('comment:deleting', comment) === false) {
                    return false;
                }

                $el.addClass('disabled').prop('disabled',  true);
                this.$http.delete(this.post_route + '/' + comment.id).then(function () {
                    var index = $scope.comments.indexOf(comment);
                    if(index > -1) {
                        $scope.comments.splice(index, 1);
                    }

                    $Bus.$emit('comment:deleted', comment);
                    $el.removeClass('disabled').prop('disabled',  false);
                }, function(){
                    notify({'text' : 'An error occurred, please try again.'});
                }).then(function(){

                });
            },
            resetCommentForm : function(){
                this.prepareCommentForm();
                $('[data-toggle="emojione"]').emojioneArea()[0].emojioneArea.setText('').trigger('change');
                this.newcomment = null;
            }
        },
        components: {
            'timestamp' : Timestamp,
            'spinny' : Spinny
        }
    }
</script>
