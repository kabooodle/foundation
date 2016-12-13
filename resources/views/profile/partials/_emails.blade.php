<div id="emails">
    <emails
        :emails="{{ json_encode($_emails) }}"
        new-email-endpoint="{{ apiRoute('user.emails.store', user()->id) }}"
    ></emails>
    {{--@foreach($_emails as $_email)--}}
        {{--<email--}}
            {{--:is-input="{{ json_encode(false) }}"--}}
            {{--address="{{ $_email->address }}"--}}
            {{--:is-primary="{{ json_encode($_email->isPrimary()) }}"--}}
            {{--:is-verified="{{ json_encode($_email->isVerified()) }}"--}}
        {{--></email>--}}
    {{--@endforeach--}}


</div>

@push('footer-scripts')
<script src="/assets/js/emails.js"></script>
@endpush