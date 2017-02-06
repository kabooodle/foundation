<div id="emails">

    <emails
        :initial-emails="{{ $_emails->toJson()  }}"
        :initial-primary-id="{{ $_primary_email->id }}"
        emails-endpoint="{{ apiRoute('user.emails.index', webUser()->id) }}"
        new-email-endpoint="{{ apiRoute('user.emails.store', webUser()->id) }}"
        update-primary-endpoint="{{ apiRoute('user.emails.update-primary', webUser()->id) }}"
        resend-verification-endpoint="{{ apiRoute('user.emails.resend-verification', webUser()->id) }}"
    ></emails>

</div>

@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/emails.js') }}"></script>
@endpush