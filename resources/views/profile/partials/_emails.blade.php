<div id="emails">

    <emails
        :initial-emails="{{ $_emails->toJson()  }}"
        :initial-primary-id="{{ $_primary_email->id }}"
        emails-endpoint="{{ apiRoute('user.emails.index', user()->id) }}"
        new-email-endpoint="{{ apiRoute('user.emails.store', user()->id) }}"
        update-primary-endpoint="{{ apiRoute('user.emails.update-primary', user()->id) }}"
        resend-verification-endpoint="{{ apiRoute('user.emails.resend-verification', user()->id) }}"
    ></emails>

</div>

@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/emails.js') }}"></script>
@endpush