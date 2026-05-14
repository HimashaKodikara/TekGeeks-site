<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acknowledgement of Your NIC Misuse Report</title>
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
                                <img src="{{ asset('frontend/images/chat.svg') }}" width="50" height="50" alt="Received" style="display:block; margin-left: auto; margin-right: auto;" />
                            </div>
                            <h1 style="margin: 0 0 8px 0; color: #2c3e50; font-size: 20px; font-weight: bold;">Acknowledgement of Your NIC Misuse Report</h1>
                            <p style="margin: 0; color: #7f8c8d; font-size: 14px;">Reference No: <strong style="color: #2c3e50; font-family: 'Courier New', monospace;">{{ $mailBodyContent['reference_no'] }}</strong></p>
                        </td>
                    </tr>

                    <!-- Greeting & Intro -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <p style="margin: 0; color: #555555; font-size: 14px; line-height: 1.6;">
                                Dear Sir / Madam,<br><br>
                                Thank you for contacting the Commission to Investigate Allegations of Bribery or Corruption (CIABOC) regarding the suspected unauthorized use of your National Identity Card (NIC) / Passport within the Declaration of Assets &amp; Liabilities System.<br><br>
                                We confirm that your report has been successfully received and recorded with the following details.
                            </p>
                        </td>
                    </tr>

                    <!-- Report Details Box -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f8f9fa; border-radius: 6px;">
                                <tr>
                                    <td colspan="2" style="padding: 12px 16px 8px 16px; color: #323232; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #e0e0e0;">
                                        Report Submission Details
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 16px 6px 16px; color: #323232; font-size: 14px; font-weight: bold; width: 45%;">Reference Number:</td>
                                    <td style="padding: 10px 16px 6px 16px; color: #2c3e50; font-size: 14px; font-family: 'Courier New', monospace; font-weight: bold;">{{ $mailBodyContent['reference_no'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 16px 6px 16px; color: #323232; font-size: 14px; font-weight: bold;">Date &amp; Time of Submission:</td>
                                    <td style="padding: 6px 16px 6px 16px; color: #616161; font-size: 14px;">{{ !empty($mailBodyContent['created_at']) ? (new \DateTime($mailBodyContent['created_at']))->format('Y-m-d H:i:s') : '' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 16px 6px 16px; color: #323232; font-size: 14px; font-weight: bold;">NIC / Passport Number:</td>
                                    <td style="padding: 6px 16px 6px 16px; color: #616161; font-size: 14px;">{{ $mailBodyContent['national_id_number'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 16px 6px 16px; color: #323232; font-size: 14px; font-weight: bold;">Email Address:</td>
                                    <td style="padding: 6px 16px 6px 16px; color: #616161; font-size: 14px;">{{ $mailBodyContent['email'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 6px 16px 14px 16px; color: #323232; font-size: 14px; font-weight: bold;">Mobile Number:</td>
                                    <td style="padding: 6px 16px 14px 16px; color: #616161; font-size: 14px;">{{ $mailBodyContent['country_code'].' '.$mailBodyContent['mobile_number'] }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Reason Provided Box -->
                    <tr>
                        <td style="padding: 0 40px 20px 40px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f8f9fa; border-radius: 6px;">
                                <tr>
                                    <td style="padding: 12px 16px 8px 16px; color: #323232; font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #e0e0e0;">
                                        Reason Provided
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 14px 16px; color: #555555; font-size: 14px; line-height: 1.6; font-style: italic;">
                                        {{ $mailBodyContent['comment'] }}
                                    </td>
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
                                        <p style="margin: 0 0 6px 0; color: #27ae60; font-size: 13px; font-weight: bold;">✔ What's Next</p>
                                        <p style="margin: 0; color: #666666; font-size: 13px; line-height: 1.5;">
                                            Your report will be reviewed by the relevant officers of CIABOC. If additional verification or documentation is required, you may be contacted through the details you provided. Please retain your reference number for any future communication regarding this matter.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Confidentiality Notice -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #fef9f0; border-radius: 6px; border-left: 4px solid #f39c12;">
                                <tr>
                                    <td style="padding: 14px 16px;">
                                        <p style="margin: 0 0 6px 0; color: #e67e22; font-size: 13px; font-weight: bold;">🔒 Confidentiality Assurance</p>
                                        <p style="margin: 0; color: #666666; font-size: 13px; line-height: 1.5;">
                                            All information submitted will be treated confidentially and used solely for verification and investigation purposes in accordance with applicable laws and regulations. If you require further assistance, please contact CIABOC through the official communication channels.
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
                                Thank you for bringing this matter to our attention.<br><br>
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
