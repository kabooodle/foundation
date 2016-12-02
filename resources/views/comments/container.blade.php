
    <comments-index
            :modelobject="{{ $comment_model->toJson() }}"
            comments_url="{{ $comment_index_route  }}"
            post_route="{{ $comment_post_route }}"
    ></comments-index>