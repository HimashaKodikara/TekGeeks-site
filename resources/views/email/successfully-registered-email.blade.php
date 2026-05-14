<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Account Created</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f4f4f4;">
        <tr>
            <td style="padding: 40px 20px;">
                <table role="presentation" style="width: 600px; margin: 0 auto; border-collapse: collapse; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background-color: #132436; padding: 10px; text-align: center;">
                            <img src="{{ 'https://support.ciaboc.gov.lk/frontend/images/logo.png' }}" alt="Logo" style="height: 90px; width: auto;">
                        </td>
                    </tr>

                    <!-- Title -->
                    <tr>
                        <td style="padding: 40px 40px 20px 40px; text-align: center;">
                            <h1 style="margin: 0; color: #2c3e50; font-size: 20px; font-weight: bold;">Welcome to the Declaration of Assets and Liabilities!</h1>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <p style="margin: 0 0 20px 0; color: #555555; font-size: 14px; line-height: 1.6; text-align: center;">
                                Your account has been successfully created. You can now begin your declaration securely and conveniently online. Please use the credentials below to log in.
                            </p>
                        </td>
                    </tr>

                    <!-- Login URL Box -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f8f9fa; border-radius: 6px; border: 2px dashed #3498db;">
                                <tr>
                                    <td style="padding: 20px 24px; text-align: center;">
                                        <p style="margin: 0 0 10px 0; color: #484848; font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">System Link</p>
                                        <a target="_blank" href="https://ald.ciaboc.gov.lk/" style="font-size: 16px; font-weight: 600; color: #126aa4; word-break: break-all; text-decoration: none;">
                                            https://ald.ciaboc.gov.lk
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Credentials Box -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f8f9fa; border-radius: 6px;">
                                <tr>
                                    <td colspan="2" style="padding: 12px 16px 10px 16px; color: #323232; font-size: 13px; font-weight: bold; text-transform: uppercase; border-bottom: 1px solid #e0e0e0;">
                                        Your Login Credentials
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 16px 6px 16px; color: #323232; font-size: 14px; font-weight: bold; width: 30%;">NIC Number:</td>
                                    <td style="padding: 10px 16px 6px 16px; color: #2c3e50; font-size: 14px; font-weight: bold;">{{ $nic }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 16px 14px 16px; color: #323232; font-size: 14px; font-weight: bold;">Password:</td>
                                    <td style="padding: 6px 16px 14px 16px; color: #2c3e50; font-size: 13px;"><i>The password you provided at the time of registration</i></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Security Notice -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #fff8e1; border-radius: 6px; border-left: 4px solid #f39c12;">
                                <tr>
                                    <td style="padding: 14px 16px;">
                                        <p style="margin: 0 0 6px 0; color: #e67e22; font-size: 13px; font-weight: bold;">⚠ Security Reminder</p>
                                        <p style="margin: 0; color: #666666; font-size: 13px; line-height: 1.5;">
                                            Please do not share your password or One-Time Password (OTP) with anyone. Always keep your account information confidential to protect your data.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <hr style="border: none; border-top: 1px solid #eeeeee; margin: 0;">
                        </td>
                    </tr>

                    <!-- Support Note -->
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
