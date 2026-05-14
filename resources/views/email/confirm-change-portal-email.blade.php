<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Information Successfully Updated</title>
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
                            <div style="margin-bottom: 16px;">
                                <img src="{{ asset('frontend/images/success.png') }}" width="72" height="72" alt="Success" style="display:block; margin-left: auto; margin-right: auto;" />
                            </div>
                            <h1 style="margin: 0 0 8px 0; color: #2c3e50; font-size: 20px; font-weight: bold;">Contact Information Successfully Updated</h1>
                            <p style="margin: 0; color: #7f8c8d; font-size: 14px;">Declaration of Assets &amp; Liabilities System</p>
                        </td>
                    </tr>

                    <!-- Greeting & Intro -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <p style="margin: 0; color: #555555; font-size: 14px; line-height: 1.6;">
                                Dear Declarant,<br><br>
                                This is to inform you that your Email address in the Declaration of Assets &amp; Liabilities System has been successfully updated.
                            </p>
                        </td>
                    </tr>

                    <!-- Updated Detail Box -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f8f9fa; border-radius: 6px;">
                                <tr>
                                    <td colspan="2" style="padding: 12px 16px 8px 16px; color: #323232; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #e0e0e0;">
                                        Updated Detail
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 16px 6px 16px; color: #323232; font-size: 14px; font-weight: bold; width: 40%;">Field Updated:</td>
                                    <td style="padding: 10px 16px 6px 16px; color: #616161; font-size: 14px;">Email Address</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 16px 6px 16px; color: #323232; font-size: 14px; font-weight: bold;">New Email Address:</td>
                                    <td style="padding: 6px 16px 6px 16px; color: #2c3e50; font-size: 14px; font-weight: bold;">{{ $mailBodyContent['new_email'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 16px 14px 16px; color: #323232; font-size: 14px; font-weight: bold;">Updated On:</td>
                                    <td style="padding: 6px 16px 14px 16px; color: #616161; font-size: 14px;">{{ $mailBodyContent['updated_date'] }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Confirmation Banner -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #eafaf1; border-radius: 6px; border-left: 4px solid #27ae60;">
                                <tr>
                                    <td style="padding: 14px 16px;">
                                        <p style="margin: 0 0 6px 0; color: #27ae60; font-size: 13px; font-weight: bold;">✔ Update Confirmed</p>
                                        <p style="margin: 0; color: #666666; font-size: 13px; line-height: 1.5;">
                                            Your registered contact details have been updated in the system. Please ensure that your contact information remains secure and accessible for future system communications.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Security Warning -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #fdf2f2; border-radius: 6px; border-left: 4px solid #c0392b;">
                                <tr>
                                    <td style="padding: 14px 16px;">
                                        <p style="margin: 0 0 6px 0; color: #c0392b; font-size: 13px; font-weight: bold;">⚠ Did Not Make This Change?</p>
                                        <p style="margin: 0; color: #666666; font-size: 13px; line-height: 1.5;">
                                            If this change was not made by you, please report the incident immediately to the relevant authorities and contact our system administrator to secure your account without delay.
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
