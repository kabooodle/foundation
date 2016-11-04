<?php

$message = Messages::retrieve();

$types = [
        'error' => 'error',
        'info' => 'info',
        'warn' => 'warning',
        'success' => 'success'
];

if ($message instanceof \Kabooodle\Libraries\Messages\MessagesInterface) :
    foreach ($types as $key => $v) :
        if ($message->has($key)) :
            $message->setFormat("
<script>
$(function(){ var n = noty({
    text: ':message',
        layout: 'top',
        theme: 'relax',
        type: '" . $v . "',
        animation: {
            open: 'animated bounceInDown',
            close: 'animated bounceOutUp'
        },
        timeout: 4000,
        closeWith: ['button','click']
    });
});
</script>
");
            echo implode('', $message->get($key));
        endif;
    endforeach;
endif;