<div class="box">
    <div class="box-header">
        <h3>Comments <span class="label success" id="comments_count">{{ $comment_model->comments->count() }}</span></h3>
    </div>
    <div class="box-body">
        <div class="streamline b-l m-l-md" id="comments_container">
            @if($comment_model->comments->count() > 0)
                @foreach($comment_model->comments as $_comment)
            @include('comments.partials._comment')
                @endforeach
            @endif
        </div>
        @include('comments.partials._post')
    </div>
</div>

@push('footer-scripts')

<script>
    $(function(){
        var form = $('#comment_new_form');

        form.submit(function(e){
            e.preventDefault();
            var $this = $(this),
                    $submitBtn = $this.find('#comment_new_submit_btn'),
                    $textInput = $this.find('#comment_new_text'),
                    $commentsContainer = $('#comments_container'),
                    $commentsCountEl = $('#comments_count'),
                    data = $this.serialize();

            if (!$textInput.val() || $textInput.val() == '') {
                alert('Comment cannot be empty');
                $submitBtn.prop('disabled', false).removeClass('disabled');
                return;
            }

            $textInput.prop('disabled', true).addClass('disabled');

            $.ajax({
                method : 'POST',
                url : $this.prop('action'),
                data: data
            }).success(function(response, statusText, xhr){
                $(response.html).appendTo($commentsContainer);
                $commentsCountEl.html(parseInt(response.total));
            }).error(function(response){
                alert('An error occured, please try again.');
            }).always(function(){
                $submitBtn.prop('disabled', false).removeClass('disabled');
                $textInput.prop('disabled', false).removeClass('disabled').val('');
            });

            return false;
        });

    });
</script>

@endpush