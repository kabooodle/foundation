<div class="box m-a-0 b-a">
    <form id="comment_new_form" method="POST" action="{{ $comment_post_route }}" accept-charset="UTF-8">
        <textarea id="comment_new_text" name="text_raw" class="form-control no-border" rows="3" placeholder="Type something..."></textarea>
        <div class="box-footer clearfix">
            <button id="comment_new_submit_btn" type="submit" class="btn success pull-right btn-sm">Post Comment</button>
        </div>
    </form>
</div>