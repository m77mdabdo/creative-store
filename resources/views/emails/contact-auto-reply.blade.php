<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>We Received Your Message</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.8; color: #333;">
    <h2>Hello {{ $contactMessage->name }},</h2>

    <p>Thank you for contacting Creative Store.</p>

    <p>We have received your message and our team will get back to you as soon as possible.</p>

    <p><strong>Your message details:</strong></p>

    <p>
        <strong>Subject:</strong> {{ $contactMessage->subject ?: 'No Subject' }}<br>
        <strong>Message:</strong> {{ $contactMessage->message }}
    </p>

    <p>Best regards,<br>Creative Store Team</p>
</body>
</html>
