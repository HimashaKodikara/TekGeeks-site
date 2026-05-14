<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
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
                            <h1 style="margin: 0; color: #2c3e50; font-size: 20px; font-weight: bold;">Contact Information Update</h1>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <!-- Greeting & Intro -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <p style="margin: 0; color: #555555; font-size: 14px; line-height: 1.6;">
                                Dear Declarant,<br><br>
                                A request has been received to update the email address associated with your Declaration of Assets &amp; Liabilities profile. Please use the One-Time Password (OTP) below to verify the new contact details.
                            </p>
                        </td>
                    </tr>

                    <!-- OTP Code Box -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f8f9fa; border-radius: 8px; border: 2px dashed #3498db;">
                                <tr>
                                    <td style="padding: 30px; text-align: center;">
                                        <div style="font-size: 42px; font-weight: bold; color: #2c3e50; letter-spacing: 8px; font-family: 'Courier New', monospace;">
                                            {{ $otp }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Expiration Notice -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center;">
                            <p style="margin: 0 0 10px 0; color: #e74c3c; font-size: 14px; font-weight: bold;">
                                This code will expire in 5 minutes
                            </p>
                            <p style="margin: 0; color: #666666; font-size: 14px; line-height: 1.5;">
                                For security purposes, please do not share this code with anyone.
                            </p>
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="padding: 0 40px;">
                            <hr style="border: none; border-top: 1px solid #e0e0e0; margin: 20px 0;">
                        </td>
                    </tr>

                    <!-- Security Notice -->
                    <tr>
                        <td style="padding: 20px 40px 40px 40px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #fff3cd; border-radius: 6px; border-left: 4px solid #ffc107;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 10px 0; color: #856404; font-size: 14px; font-weight: bold;">
                                            Didn’t request this login?
                                        </p>
                                        <p style="margin: 0; color: #856404; font-size: 13px; line-height: 1.5;">
                                            If you did not attempt to update Email address, please ignore this email.
                                            If you believe your account security may be at risk, we recommend changing your password immediately and contacting the Commission through the official support channels.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center;">
                            <p style="margin-top: 0; color: #888888; font-size: 13px; line-height: 1.6;">
                                This is an automated notification. Do not reply directly to this email.<br>For any queries regarding your amendment, please contact our system administrator.
                            </p>

                            <p style="margin: 0; font-size: 14px;">
                                <a href="tel:+94112587287" style="color: #393939; margin-right: 10px;">+94 11 258 7287</a>
                                <a href="tel:+94767011954" style="color: #393939; margin-right: 10px;">+94 76 701 1954</a>
                                <a href="mailto:ald@ciaboc.gov.lk" style="color: #393939;">ald@ciaboc.gov.lk</a>
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
