<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
</head>
<body>
<table cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td style="font-family:Arial,sans-serif; font-size:16px; color:#111111; line-height:1.6;">
            <p>Hi {{ $user->name }},</p>
            <p>Click the link below to reset your password. It expires in 1 hour.</p>
            <p><a href="{{ $resetUrl }}">Reset your password</a></p>
            <p>If you didn't request this, you can safely ignore this email.</p>
        </td>
    </tr>
</table>
</body>
</html>
