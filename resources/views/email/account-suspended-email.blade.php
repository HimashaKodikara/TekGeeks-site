<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Important Notice Regarding Your Account</title>
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
                                <img src="warning.png" width="60" height="60" alt="Success" style="display:block; margin-left: auto; margin-right: auto;" />
                            </div>
                            <h1 style="margin: 0 0 8px 0; color: #2c3e50; font-size: 20px; font-weight: bold;">Important Notice Regarding Your Account</h1>
                            <p style="margin: 0; color: #7f8c8d; font-size: 14px;">Declaration of Assets &amp; Liabilities System</p>
                        </td>
                    </tr>

                    <!-- Greeting & Intro -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <p style="margin: 0; color: #555555; font-size: 14px; line-height: 1.6;">
                                Dear Sir / Madam,<br><br>
                                This is to inform you that the account created under the Declaration of Assets &amp; Liabilities System using National Identity Card (NIC) / Passport details associated with another individual has been <strong style="color: #c0392b;">suspended</strong> following an official review conducted by the Commission to Investigate Allegations of Bribery or Corruption (CIABOC).<br><br>
                                Our investigation indicates that the account was registered using identification details that do not belong to you.
                            </p>
                        </td>
                    </tr>

                    <!-- Account Status Box -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f8f9fa; border-radius: 6px;">
                                <tr>
                                    <td colspan="2" style="padding: 12px 16px 8px 16px; color: #323232; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #e0e0e0;">
                                        Account Status
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 16px 6px 16px; color: #323232; font-size: 14px; font-weight: bold; width: 45%;">NIC / Passport Used:</td>
                                    <td style="padding: 10px 16px 6px 16px; color: #616161; font-size: 14px;">{{ $complaintRelatedDeclarantDetails['nic'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 16px 6px 16px; color: #323232; font-size: 14px; font-weight: bold;">Suspended On:</td>
                                    <td style="padding: 6px 16px 6px 16px; color: #616161; font-size: 14px;">{{ $complaintRelatedDeclarantDetails['suspended_at'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 16px 14px 16px; color: #323232; font-size: 14px; font-weight: bold;">Account Status:</td>
                                    <td style="padding: 6px 16px 14px 16px;">
                                        <span style="background-color: #fdf2f2; color: #c0392b; font-size: 12px; font-weight: bold; padding: 3px 10px; border-radius: 12px; border: 1px solid #c0392b;">
                                            ● SUSPENDED
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Legal Notice -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #fdf2f2; border-radius: 6px; border-left: 4px solid #c0392b;">
                                <tr>
                                    <td style="padding: 14px 16px;">
                                        <p style="margin: 0 0 6px 0; color: #c0392b; font-size: 13px; font-weight: bold;">⚠ Legal Notice</p>
                                        <p style="margin: 0; color: #666666; font-size: 13px; line-height: 1.5;">
                                            Please note that providing or using another person's identification information without proper authorization may constitute a violation of applicable laws and regulations.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Action Taken Box -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f8f9fa; border-radius: 6px;">
                                <tr>
                                    <td style="padding: 12px 16px 8px 16px; color: #323232; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #e0e0e0;">
                                        Action Taken
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 16px 4px 28px; color: #555555; font-size: 14px; line-height: 2;">
                                        &bull;&nbsp; Your account has been suspended with immediate effect.<br>
                                        &bull;&nbsp; Access to the Declaration of Assets &amp; Liabilities System through this account is no longer permitted.
                                    </td>
                                </tr>
                                <tr><td style="padding: 8px 0;"></td></tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Dispute / Clarification Notice -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #fef9f0; border-radius: 6px; border-left: 4px solid #f39c12;">
                                <tr>
                                    <td style="padding: 14px 16px;">
                                        <p style="margin: 0 0 6px 0; color: #e67e22; font-size: 13px; font-weight: bold;">💬 Believe This Is an Error?</p>
                                        <p style="margin: 0; color: #666666; font-size: 13px; line-height: 1.5;">
                                            If you believe this action has been taken in error or you wish to provide clarification, you may contact CIABOC through the official communication channels. Please be advised that further action may be taken in accordance with applicable laws if misuse of identity information is confirmed.
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
                                If you have already submitted your declaration, please disregard this notice. For assistance, contact your system administrator. Do not reply directly to this email.
                            </p>

                            <p style="margin: 0; font-size: 14px;">
                               <a href="tel:+94112587287" style="color: #393939; margin-right: 10px;">+94 11 258 7287</a>
                               <a href="tel:+94767011954" style="color: #393939; margin-right: 10px;">+94 76 701 19 54</a>
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
