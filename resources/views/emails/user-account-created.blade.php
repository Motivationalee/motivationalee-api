<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account Credentials</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f5;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f4f4f5;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;background-color:#ffffff;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="padding:28px 32px;background-color:#111827;">
                            <h1 style="margin:0;font-size:24px;line-height:1.35;font-weight:700;color:#ffffff;">
                                Your Account Has Been Created
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px;">
                            <p style="margin:0 0 20px;font-size:16px;line-height:1.6;color:#374151;">
                                Hi <strong style="color:#111827;">{{ $user->name }}</strong>,
                            </p>
                            <p style="margin:0 0 20px;font-size:16px;line-height:1.6;color:#374151;">
                                An account has been created for you on
                                <strong style="color:#111827;"><a href="{{ config('app.web_app_url') }}" target="_blank">{{ config('app.name') }}</a></strong>.
                                Use the credentials below to sign in.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;">
                                <tr>
                                    <td style="padding:20px 24px;">
                                        <p style="margin:0 0 14px;font-size:16px;line-height:1.6;">
                                            <strong style="display:inline-block;min-width:100px;color:#111827;">Email:</strong>
                                            <span style="color:#374151;">{{ $user->email }}</span>
                                        </p>
                                        <p style="margin:0;font-size:16px;line-height:1.6;">
                                            <strong style="display:inline-block;min-width:100px;color:#111827;">Password:</strong>
                                            <span style="color:#111827;font-family:Consolas,Monaco,monospace;font-size:18px;letter-spacing:0.5px;">{{ $plainPassword }}</span>
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:24px 0 0;font-size:16px;line-height:1.6;color:#374151;">
                                For your security, please change this password after your first login.
                            </p>

                            <p style="margin:20px 0 0;font-size:16px;line-height:1.6;color:#374151;">
                                Thanks,<br>
                                <strong style="color:#111827;">{{ config('app.name') }}</strong>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
