<div id="comments">

    <commentable
            :modelobject="{{ $comment_model->toJson() }}"
            post_route="{{ $comment_post_route }}"
            :comments="{{ $comment_model->comments->toJson() }}"
    ></commentable>

    <script type="text/x-template" id="comments-template">
        <div v-if="comments_ready">
            <div class="box">
                <div class="box-header clearfix">
                    <h3 class="pull-left">Comments <span class="label grey-400 text-white" id="comments_count">@{{ comments.length }}</span></h3>
                    {{--<div class="pull-right">--}}
                        {{--<button id="comment_delete_all_btn" data-model-id="@{{  modelId  }}" type="button" class="btn white btn-xs text-muted">Delete All</button>--}}
                    {{--</div>--}}
                </div>
                <div class="box-body">
                    <div class="streamline b-l m-l-md" id="comments_container">
                        <div v-for="comment in comments">
                            <div class="sl-item" data-id="@{{ comment.uuid }}" data-author-id="@{{ comment.author.public_hash }}" data-author="@{{ comment.author.name }}">
                                <div class="sl-left">
                                    <img src="https://unsplash.it/32/32/?random" class="img-circle">
                                </div>
                                <div class="sl-content">
                                    <div class="sl-author">
                                        <a href="" class="_600">@{{ comment.author.name }}</a>
                                    </div>
                                    <div>
                                        @{{{ comment.text }}}
                                    </div>
                                    <div class="sl-footer sl-date clearfix">
                                        <ul class="text-muted list-inline pull-left">
                                            <li class="list-inline-item"><time datetime="@{{ comment.created_at.date }}">@{{ comment.created_at.date | diffhumans }}</time></li>
                                            <li class="list-inline-item" v-if="userCanDelete(comment)"><button type="button" class="white btn btn-text btn-xs" v-on:click="deleteComment(comment, $event)">Delete</button></li>
                                        </ul>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box m-a-0 b-a">
                        <form id="comment_new_form" method="POST" action="@{{ post_route }}" accept-charset="UTF-8" v-on:submit="addNewComment">
                            <textarea id="comment_new_text" v-model="comment" name="text_raw" data-toggle="emojione" class="form-control no-border" rows="3" placeholder="Type something..."></textarea>
                            <div class="box-footer clearfix">
                                <button id="comment_new_submit_btn" type="submit" class="btn primary pull-right btn-sm" :disabled="!comment" >Post Comment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="center-block text-center">
                <i class="fa fa-spinner fa-spin fa-1x"></i>
            </div>
        </div>
    </script>

</div>


@push('footer-scripts')
<script>
    Vue.filter('diffhumans', function (value) {
        return moment(value).fromNow();
    });

    Vue.component('commentable', {
        props:  ["comments", "post_route", "comments_ready", "modelobject"],
        template: '#comments-template',
        data: function () {
            return {
                comments: [],
                comments_ready: false,
                comment: null,
                modelobject: {}
            }
        },
        ready : function(){
            var $scope = this;
            $(function(){
                $('[data-toggle="emojione"]').emojioneArea({
                    pickerPosition: "bottom",
                    filtersPosition: "bottom",
                    autocomplete : true,
                    saveEmojisAs: "shortname",
                    events: {
                        keyup: function (editor, event) {
                            $scope.comment = this.getText();
                        }
                    }
                });
            });
            this.comments_ready = true;
        },
        methods : {
            userCanDelete : function(comment){
                return comment.author.id === this.modelobject.user.id || KABOOODLE_APP.currentUser.id === this.modelobject.user.id || comment.author.id === KABOOODLE_APP.currentUser.id
            },
            addNewComment : function(e){
                e.preventDefault();
                var $el = $(e.target);
                var that = this;

                this.$http.post(this.post_route, {text_raw : $('#comment_new_text').val()}).then(function (response) {
                    that.comments.push($.parseJSON(response.body.json));
                    that.resetCommentForm();
                }, function(response){
                    alert('an error occurred, please try again.');
                    that.resetCommentForm();
                });
            },
            deleteComment: function(comment, e){
                e.preventDefault();
                if (!this.userCanDelete(comment)) {
                    return false;
                }
                var $el = $(e.target);
                var that = this;

                if (this.$dispatch('comment:deleting', comment) === false) {
                    return false;
                }

                $el.addClass('disabled').prop('disabled',  true);
                this.$http.delete(this.post_route + '/' + comment.id).then(function (response) {
                    that.comments.$remove(comment);
                    that.$dispatch('comment:deleted', comment);
                }, function(response){
                    alert('an error occurred, please try again.');
                });
            },
            resetCommentForm : function(){
                $('[data-toggle="emojione"]').emojioneArea()[0].emojioneArea.setText('').trigger('change');
                this.comment = false;
            }
        }
    });

    new Vue({
        el: '#comments',
        ready : function(){
            console.log('Commentable ready.');
        }
    });
</script>
@endpush