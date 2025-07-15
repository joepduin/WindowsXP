<?php
return [
    // General settings
    'mail'       => 'mail@domain.com',
    'from_mail'  => 'no-reply@domain.com',
    'from_name'  => 'Your Name',

    // Allowed domains
    'allowed_domains' => ['domain.com', 'www.domain.com', 'sub.domain.com'],

    // reCAPTCHA
    'recaptcha_enabled'  => true, // Currently, this setting has no effect and cannot be used to disable reCAPTCHA.
    'recaptcha_secret'   => 'CHANGE_THIS',
    // Please update the secrets in ./windows/contact.html (r5,15)

    // Mail texts
    'subject_prefix'     => 'Request: ',
    'mail_heading'       => 'Thank you for contacting me!',
    'mail_intro'         => 'Hi,',
    'mail_body'          => 'I have received your message and will get back to you within 24 to 48 hours on working days. Please note that I\'m not available during weekends and public holidays.',
    'mail_signature'     => "Kind regards,<br>Your Name",
    'mail_footer_notice' => 'This message was sent to {{email}} and is intended for them only. If you received this message but did not initiate contact, you don\'t need to do anything. However, if you believe this message was sent in error or if you have concerns, please report it to <a href="mailto:report@domain.com">report@domain.com</a> or use the contact form on the <a href="https://domain.com/contact" target="_blank">website</a>.',

    // Error messages
    'error_invalid_domain'  => 'Unauthorized request. Please only trust the (sub.)domain.com domain. Not working? Just mail us: {{mail}}',
    'error_recaptcha_failed'=> 'reCAPTCHA verification failed. Please try again. Not working? Just mail us: {{mail}}',
    'error_invalid_email'   => 'Invalid mail address. Not working? Just mail us: {{mail}}',
    'error_send_failed'     => 'Error, try again or just mail us: {{mail}}',

    // Success message
    'success_message'       => 'Done! Check your (spam) inbox for more details.',
];