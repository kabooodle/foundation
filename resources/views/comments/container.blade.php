<div id="comments-index">
    <comments-index
            :modelobject="{{ $comment_model->toJson() }}"
            comments_url="{{ $comment_index_route  }}"
            post_route="{{ $comment_post_route }}"
    ></comments-index>
</div>

@push('footer-scripts')
<script src="/assets/js/comments-index.js"></script>
@endpush