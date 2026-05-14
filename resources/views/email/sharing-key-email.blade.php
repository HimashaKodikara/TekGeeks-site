<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sharing Key Email</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f4f4f4;">
        <tr>
            <td style="padding: 40px 20px;">
                <table role="presentation" style="width: 600px; margin: 0 auto; border-collapse: collapse; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background-color: #132436; padding: 10px; text-align: center;">
                            <img src="{{ asset('frontend/images/logo.png') }}" alt="Logo" style="height: 90px; width: auto;">
                        </td>
                    </tr>

                    <!-- Title -->
                    <tr>
                        <td style="padding: 40px 40px 20px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #2c3e50; font-size: 20px; font-weight: bold;">Sharing Key</h1>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center;">
                            <p style="margin: 0 0 20px 0; color: #555555; font-size: 14px; line-height: 1.4;">
                                A declarant has shared selected information with you. This sharing key allows you to <strong>import common covered person details</strong> into your own declaration, subject to your review and confirmation. Please use the sharing key below to proceed.
                            </p>
                        </td>
                    </tr>

                    <!-- Sender and Purpose Info -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f8f9fa; border-radius: 6px; padding: 20px;">
                                <tr>
                                    <td style="padding: 6px 12px; color: #323232; font-size: 14px; font-weight: bold; width: 25%;">Sent by:</td>
                                    <td style="padding: 6px 12px; color: #616161; font-size: 14px;">{{ $mailBodyContent['sent_by'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 12px; color: #323232; font-size: 14px; font-weight: bold;">Generated at:</td>
                                    <td style="padding: 6px 12px; color: #616161; font-size: 14px;">{{ \Carbon\Carbon::parse($mailBodyContent['generated_at'])->format('g:i A, F j, Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 12px; color: #323232; font-size: 14px; font-weight: bold;">Expires at:</td>
                                    <td style="padding: 6px 12px; color: #616161; font-size: 14px;">{{ \Carbon\Carbon::parse($mailBodyContent['expiring_at'])->format('g:i A, F j, Y') }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Sharing Key Box -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f8f9fa; border-radius: 8px; border: 2px dashed #3498db;">
                                <tr>
                                    <td style="padding: 30px; text-align: center;">
                                        <div style="font-size: 36px; font-weight: bold; color: #2c3e50; letter-spacing: 4px; font-family: 'Courier New', monospace; word-break: break-all;">
                                            {{ $mailBodyContent['sharing_key'] }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Expiration Notice -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px; text-align: center;">
                            <p style="margin: 0 0 10px 0; color: #e74c3c; font-size: 14px; font-weight: bold;">
                                This sharing key will expire in 48 hours
                            </p>
                            <p style="margin: 0 0 15px 0; color: #666666; font-size: 14px; line-height: 1.5;">
                                Please use this key before it expires.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #34495e; padding: 20px; text-align: center;">
                            <p style="margin: 0; color: #ffffff; font-size: 12px;">
                                © {{ now()->year }} CIABOC. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
