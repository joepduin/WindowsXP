<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $myip = 'change-secret';
    $mail = 'mail@joepduin.nl';
    $allowedDomains = ['joepduin.nl', 'www.joepduin.nl', 'beta.joepduin.nl', $myip];
    $referer = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) : '';

    if (!in_array($referer, $allowedDomains)) {
        echo "Unauthorized request. Please only trust the (beta.)joepduin.nl domain. Not working? Just mail us: $mail";
        exit;
    }

    $recaptchaSecret = 'change-secret';
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

    $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse");
    $responseData = json_decode($verifyResponse);

    if (!$responseData->success) {
        echo "reCAPTCHA verification failed. Please try again. Not working? Just mail us: $mail";
        exit;
    }

    $to = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $message = nl2br(htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8'));

    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid mail address. Not working? Just mail us: $mail";
        exit;
    }

    $subject = "Request: " . htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $body = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                color: #333;
            }
            .email-container {
                width: 100%;
                max-width: 600px;
                margin: 20px auto;
                background-color: #fff;
                padding: 20px;
                border: 1px solid #ddd;
                border-radius: 5px;
            }
            h1 {
                font-size: 20px;
                color: #333;
                margin-bottom: 10px;
            }
            p {
                margin: 10px 0;
                line-height: 1.6;
                color: #333;
            }
            a {
                color: #777; /* Kleur van de links is grijs */
                text-decoration: underline; /* Onderstreping behouden */
            }
            .footer {
                font-size: 12px;
                text-align: center;
                color: #777; /* Grijze kleur voor footer tekst */
                margin-top: 20px;
            }
            .footer a {
                color: #777; /* Grijze kleur voor de links in de footer */
                text-decoration: underline; /* Onderstreping voor links in de footer */
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <h1>Thank you for contacting me!</h1>
            <p>Hi,</p>
            <p>I have received your message and will get back to you within 24 to 48 hours on working days. Please note that I&apos;m not available during weekends and public holidays.</p>
            <p>Kind regards,<br>Joep Duin</p>
            <p><strong>Your message:</strong></p>
            <p>' . $message . '</p>
    
            <div class="footer">
                &copy; ' . date("Y") . ' Joep Duin. All rights reserved.<br>
                This message was sent to ' . $to . ' and is intended for him only. <br>
                If you received this message but did not initiate contact, you don&apos;t need to do anything. However, if you believe this message was sent in error or if you have concerns, please feel free to report it to <a href="mailto:report@joepduin.nl">report@joepduin.nl</a> or use the contact form on the <a href="https://beta.joepduin.nl/#contact" target="_blank">website</a>.
            </div>
        </div>
    </body>
    </html>';

    $headers = "From: Joep Duin <no-replay@joepduin.nl>\r\n";
    $headers .= "Reply-To: $mail\r\n";
    $headers .= "BCC: $mail\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    if (mail($to, $subject, $body, $headers)) {
        echo "Done! Check your (spam) inbox for more details.";
    } else {
        echo "Error, try again or just mail us: $mail";
    }
} else {
    echo "Invalid request.";
}
?>