<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your NIC Misuse Report Has Been Resolved</title>
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
                                <img src="{{ asset('frontend/images/success.png') }}" width="60" height="60" alt="Resolved" style="display:block; margin-left: auto; margin-right: auto;" />
                            </div>
                            <h1 style="margin: 0 0 8px 0; color: #2c3e50; font-size: 20px; font-weight: bold;">Your NIC Misuse Report Has Been Resolved</h1>
                        </td>
                    </tr>

                    <!-- Greeting & Intro -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <p style="margin: 0; color: #555555; font-size: 14px; line-height: 1.6;">
                                Dear Sir / Madam,<br><br>
                                Thank you for reporting a suspected unauthorized use of your National Identity Card (NIC) / Passport in the Declaration of Assets &amp; Liabilities System.
                            </p>
                        </td>
                    </tr>

                    <!-- Report Reference Box -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f8f9fa; border-radius: 6px;">
                                <tr>
                                    <td colspan="2" style="padding: 12px 16px 8px 16px; color: #323232; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #e0e0e0;">
                                        Report Details
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 16px 6px 16px; color: #323232; font-size: 14px; font-weight: bold; width: 45%;">Reference Number:</td>
                                    <td style="padding: 10px 16px 6px 16px; color: #2c3e50; font-size: 14px; font-family: 'Courier New', monospace; font-weight: bold;">{{ $mailBodyContent['reference_no'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 16px 6px 16px; color: #323232; font-size: 14px; font-weight: bold;">NIC / Passport Number:</td>
                                    <td style="padding: 6px 16px 6px 16px; color: #616161; font-size: 14px;">{{  }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 16px 14px 16px; color: #323232; font-size: 14px; font-weight: bold;">Outcome:</td>
                                    <td style="padding: 6px 16px 14px 16px;">
                                        <span style="background-color: #eafaf1; color: #27ae60; font-size: 12px; font-weight: bold; padding: 3px 10px; border-radius: 12px; border: 1px solid #27ae60;">
                                            ● RESOLVED
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Resolution Confirmation Banner -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #eafaf1; border-radius: 6px; border-left: 4px solid #27ae60;">
                                <tr>
                                    <td style="padding: 14px 16px;">
                                        <p style="margin: 0 0 6px 0; color: #27ae60; font-size: 13px; font-weight: bold;">✔ Unauthorized Account Suspended</p>
                                        <p style="margin: 0; color: #666666; font-size: 13px; line-height: 1.5;">
                                            Following a review of the information provided under Reference Number <strong>{{REFERENCE_NUMBER}}</strong>, CIABOC has confirmed that the account associated with your NIC / Passport details was created without your authorization. As a result, the account in question has been <strong>suspended</strong> to prevent further use.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Next Step Box -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f8f9fa; border-radius: 6px;">
                                <tr>
                                    <td style="padding: 12px 16px 8px 16px; color: #323232; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #e0e0e0;">
                                        Next Steps
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 14px 16px; color: #555555; font-size: 14px; line-height: 1.6;">
                                        You may now proceed to create your own declaration profile using your NIC / Passport details through the official Declaration of Assets &amp; Liabilities System.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- CTA Button -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px; text-align: center;">
                            <a href="http://assets-declaration/register" style="display: inline-block; background-color: #132436; color: #ffffff; font-size: 14px; font-weight: bold; text-decoration: none; padding: 14px 36px; border-radius: 6px; letter-spacing: 0.5px;">
                                Register My Declaration Profile →
                            </a>
                        </td>
                    </tr>

                    <!-- Support Notice -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #fef9f0; border-radius: 6px; border-left: 4px solid #f39c12;">
                                <tr>
                                    <td style="padding: 14px 16px;">
                                        <p style="margin: 0 0 6px 0; color: #e67e22; font-size: 13px; font-weight: bold;">💬 Need Assistance?</p>
                                        <p style="margin: 0; color: #666666; font-size: 13px; line-height: 1.5;">
                                            If you encounter any difficulties during registration or require further assistance, please contact CIABOC through the official support channels.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Closing Text -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <p style="margin: 0; color: #555555; font-size: 14px; line-height: 1.6;">
                                Thank you for bringing this matter to our attention and assisting us in maintaining the integrity of the system.
                            </p>
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <hr style="border: none; border-top: 1px solid #eeeeee; margin: 0;">
                        </td>
                    </tr>

                    <!-- Sign Off -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px;">
                            <p style="margin: 0; color: #555555; font-size: 14px; line-height: 1.8;">
                                Yours faithfully,<br>
                                <strong style="color: #2c3e50;">Declaration of Assets &amp; Liabilities System</strong><br>
                                <span style="color: #7f8c8d; font-size: 13px;">Commission to Investigate Allegations of Bribery or Corruption (CIABOC)</span>
                            </p>
                        </td>
                    </tr>

                    <!-- Support Note -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px; text-align: center;">
                            <p style="margin-top: 0; color: #888888; font-size: 13px; line-height: 1.6;">
                                This is an automated notification. Do not reply directly to this email.<br>For any queries regarding your amendment, please contact your system administrator.
                            </p>

                            <p style="margin: 0; font-size: 14px;">
                               <a href="tel:+94112587287" style="color: #393939; margin-right: 10px;">+94 11 258 7287 </a>
                               <a href="tel:+94767011954 " style="color: #393939; margin-right: 10px;">+94 767011954 </a>
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
