<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Verified</title>
</head>
<body>
    <h2>Your Account Has Been Successfully Verified ✅</h2>

    <p>Hello {{ $name }},</p>

    <p>Your email address has been successfully verified. You can now log in to your account.</p>

    <p>
        <a href="{{ $loginUrl }}">Login to Your Account</a>
    </p>

    <p>If you did not perform this action, please contact our support team immediately.</p>
</body>
</html>
 