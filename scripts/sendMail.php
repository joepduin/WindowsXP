<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $config = require __DIR__ . '/../config.php';

    $referer = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) : '';
    if (!in_array($referer, $config['allowed_domains'])) {
        echo str_replace('{{mail}}', $config['mail'], $config['error_invalid_domain']);
        exit;
    }

    if ($config['recaptcha_enabled']) {
        $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$config['recaptcha_secret']}&response=$recaptchaResponse");
        $response = json_decode($verify);
        if (!$response->success) {
            echo str_replace('{{mail}}', $config['mail'], $config['error_recaptcha_failed']);
            exit;
        }
    }

    $to = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
    $message = nl2br(htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8'));

    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        echo str_replace('{{mail}}', $config['mail'], $config['error_invalid_email']);
        exit;
    }

    $subject = $config['subject_prefix'] . htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

    $footer = str_replace('{{email}}', $to, $config['mail_footer_notice']);

    $body = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; color: #333; }
            .email-container { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; background-color: #fff; }
            h1 { font-size: 20px; }
            p { margin: 10px 0; line-height: 1.6; }
            a { color: #777; text-decoration: underline; }
            .footer { font-size: 12px; text-align: center; color: #777; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class="email-container">
            <h1>' . $config['mail_heading'] . '</h1>
            <p>' . $config['mail_intro'] . '</p>
            <p>' . $config['mail_body'] . '</p>
            <p>' . $config['mail_signature'] . '</p>
            <p><strong>Your message:</strong></p>
            <p>' . $message . '</p>
            <div class="footer">' . $footer . '</div>
        </div>
    </body>
    </html>';

    $headers = "From: {$config['from_name']} <{$config['from_mail']}>\r\n";
    $headers .= "Reply-To: {$config['mail']}\r\n";
    $headers .= "BCC: {$config['mail']}\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    if (mail($to, $subject, $body, $headers)) {
        echo $config['success_message'];
    } else {
        echo str_replace('{{mail}}', $config['mail'], $config['error_send_failed']);
    }
} else {
    echo "Invalid request.";
}
?>