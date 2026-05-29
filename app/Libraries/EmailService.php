<?php

namespace App\Libraries;

use Config\Services;

class EmailService
{
    /**
     * Send structured HTML email
     */
    public static function send(string $to, string $subject, string $htmlContent): bool
    {
        $email = Services::email();
        $config = new \Config\Email();

        // Ensure from is populated
        $fromEmail = !empty($config->fromEmail) ? $config->fromEmail : 'no-reply@usg-accreditation.com';
        $fromName = !empty($config->fromName) ? $config->fromName : 'USG Accreditation System';

        $email->setFrom($fromEmail, $fromName);
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage(self::wrapTemplate($subject, $htmlContent));

        // ALWAYS log emails locally in development/testing environments to easily grab links!
        $logMessage = "[" . date('Y-m-d H:i:s') . "] TO: " . $to . " | SUBJECT: " . $subject . "\n" . 
                     "CONTENT:\n" . strip_tags($htmlContent) . "\n" .
                     "RAW HTML / LINKS:\n" . $htmlContent . "\n" .
                     "========================================================================\n\n";
        
        $logDir = WRITEPATH . 'logs/';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        file_put_contents($logDir . 'email_deliveries.log', $logMessage, FILE_APPEND);

        if ($email->send()) {
            return true;
        } else {
            // Log warning on failure to help developers debug SMTP issues
            log_message('error', 'Email failed to send: ' . $email->printDebugger(['headers']));
            return false;
        }
    }

    /**
     * Send Password Reset Link Email
     */
    public static function sendPasswordReset(string $to, string $token): bool
    {
        $resetUrl = base_url('reset-password?token=' . $token);
        $subject = 'Reset Your Password - USG Accreditation';
        
        $html = "
            <p style='margin-bottom: 20px; font-size: 16px; line-height: 1.6;'>Hello,</p>
            <p style='margin-bottom: 24px; font-size: 15px; line-height: 1.6; color: #4a5568;'>
                We received a request to reset the password for your USG Accreditation System account. Click the secure button below to choose a new password. This link will expire in 1 hour.
            </p>
            <div style='text-align: center; margin: 32px 0;'>
                <a href='{$resetUrl}' style='display: inline-block; padding: 14px 30px; font-size: 15px; font-weight: 600; color: #ffffff; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); text-decoration: none; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);'>Reset Password</a>
            </div>
            <p style='margin-top: 24px; font-size: 13px; color: #718096; line-height: 1.6;'>
                If you did not request a password reset, please ignore this email or contact the administrator if you have questions.
            </p>
            <p style='margin-top: 16px; font-size: 12px; color: #a0aec0; word-break: break-all;'>
                If button doesn't work, copy-paste this URL: <br>
                <a href='{$resetUrl}' style='color: #3b82f6;'>{$resetUrl}</a>
            </p>
        ";

        return self::send($to, $subject, $html);
    }

    /**
     * Send Adviser Verification Link Email
     */
    public static function sendAdviserVerification(string $adviserEmail, array $regData): bool
    {
        $verifyUrl = base_url('validate-org/' . $regData['adviser_token']);
        $subject = 'Signature Required: Accredit New Organization - ' . $regData['name'];

        $html = "
            <p style='margin-bottom: 20px; font-size: 16px; line-height: 1.6;'>Dear Professor {$regData['adviser_name']},</p>
            <p style='margin-bottom: 24px; font-size: 15px; line-height: 1.6; color: #4a5568;'>
                You have been selected as the formal adviser for a new student organization signup: 
                <strong>{$regData['name']} ({$regData['acronym']})</strong> on the <strong>{$regData['campus']}</strong> campus.
            </p>
            <p style='margin-bottom: 24px; font-size: 15px; line-height: 1.6; color: #4a5568;'>
                To allow them to proceed to the USG Administration review phase, you must review and digitally sign their application. Please click the button below to view their full credentials and provide your digital signature.
            </p>
            <div style='text-align: center; margin: 32px 0;'>
                <a href='{$verifyUrl}' style='display: inline-block; padding: 14px 30px; font-size: 15px; font-weight: 600; color: #ffffff; background: linear-gradient(135deg, #10b981 0%, #047857 100%); text-decoration: none; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);'>Review & Sign Document</a>
            </div>
            <p style='margin-top: 16px; font-size: 12px; color: #a0aec0; word-break: break-all;'>
                If button doesn't work, copy-paste this URL: <br>
                <a href='{$verifyUrl}' style='color: #10b981;'>{$verifyUrl}</a>
            </p>
        ";

        return self::send($adviserEmail, $subject, $html);
    }

    /**
     * Send Admin Pending Verification Notice
     */
    public static function sendAdminNotice(string $adminEmail, array $regData): bool
    {
        $adminUrl = base_url('admin/organizations'); // Main portal where admin validates registrations
        $subject = 'Pending Registration: Organization Signed by Adviser';

        $html = "
            <p style='margin-bottom: 20px; font-size: 16px; line-height: 1.6;'>Hello Admin,</p>
            <p style='margin-bottom: 24px; font-size: 15px; line-height: 1.6; color: #4a5568;'>
                A new student organization registration is pending your final validation: 
                <strong>{$regData['name']} ({$regData['acronym']})</strong> - <strong>{$regData['campus']} Campus</strong>.
            </p>
            <p style='margin-bottom: 24px; font-size: 15px; line-height: 1.6; color: #4a5568;'>
                Their adviser, <strong>Professor {$regData['adviser_name']}</strong>, has successfully validated their application and signed the formal commitment forms digitally.
            </p>
            <div style='text-align: center; margin: 32px 0;'>
                <a href='{$adminUrl}' style='display: inline-block; padding: 14px 30px; font-size: 15px; font-weight: 600; color: #ffffff; background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%); text-decoration: none; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(44, 62, 80, 0.3);'>View Admin Dashboard</a>
            </div>
        ";

        return self::send($adminEmail, $subject, $html);
    }

    /**
     * Send Welcome & Credentials to Newly Approved Org
     */
    public static function sendWelcomeCredentials(string $officerEmail, string $orgName, string $username, string $plainPassword): bool
    {
        $loginUrl = base_url('login');
        $subject = 'Congratulations! Your Organization is Registered - ' . $orgName;

        $html = "
            <p style='margin-bottom: 20px; font-size: 16px; line-height: 1.6;'>Congratulations!</p>
            <p style='margin-bottom: 20px; font-size: 15px; line-height: 1.6; color: #4a5568;'>
                Your student organization registration for <strong>{$orgName}</strong> has been fully validated and approved by the USG Administration.
            </p>
            <p style='margin-bottom: 24px; font-size: 15px; line-height: 1.6; color: #4a5568;'>
                You can now log in to the Accreditation Portal to manage your documents and track your progress.
            </p>
            <div style='background: #f7fafc; border-left: 4px solid #2f855a; padding: 16px; margin: 24px 0; border-radius: 4px;'>
                <p style='margin: 0 0 8px 0; font-size: 14px;'><strong>Login Credentials:</strong></p>
                <p style='margin: 0 0 6px 0; font-size: 14px; color: #2d3748;'>Username (Email): <code>{$username}</code></p>
                <p style='margin: 0; font-size: 14px; color: #2d3748;'>Password: <code>{$plainPassword}</code></p>
            </div>
            <div style='text-align: center; margin: 32px 0;'>
                <a href='{$loginUrl}' style='display: inline-block; padding: 14px 30px; font-size: 15px; font-weight: 600; color: #ffffff; background: linear-gradient(135deg, #2f855a 0%, #22543d 100%); text-decoration: none; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(47, 133, 90, 0.3);'>Log In to Portal</a>
            </div>
            <p style='margin-top: 24px; font-size: 13px; color: #e53e3e; line-height: 1.6;'>
                <strong>Important:</strong> Please log in and change your password immediately in your account settings.
            </p>
        ";

        return self::send($officerEmail, $subject, $html);
    }

    /**
     * Wrap clean HTML templates inside a modern, highly aesthetic wrapper
     */
    private static function wrapTemplate(string $title, string $innerHtml): string
    {
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='utf-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>{$title}</title>
            </head>
            <body style='margin: 0; padding: 0; background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif;'>
                <table border='0' cellpadding='0' cellspacing='0' width='100%' style='table-layout: fixed; background-color: #f3f4f6;'>
                    <tr>
                        <td align='center' style='padding: 40px 10px;'>
                            <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);'>
                                <!-- Header Gradient Banner -->
                                <tr>
                                    <td align='center' style='background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); padding: 36px 20px;'>
                                        <h1 style='color: #ffffff; font-size: 22px; font-weight: 700; margin: 0; letter-spacing: 0.5px;'>USG ACCREDITATION</h1>
                                        <p style='color: rgba(255, 255, 255, 0.85); font-size: 13px; margin: 6px 0 0 0;'>Sultan Kudarat State University</p>
                                    </td>
                                </tr>
                                <!-- Main Body -->
                                <tr>
                                    <td style='padding: 40px 30px; color: #2d3748;'>
                                        {$innerHtml}
                                    </td>
                                </tr>
                                <!-- Footer -->
                                <tr>
                                    <td align='center' style='background-color: #f7fafc; padding: 24px 20px; border-top: 1px solid #edf2f7; color: #718096; font-size: 12px; line-height: 1.5;'>
                                        <p style='margin: 0 0 4px 0;'><strong>SKSU Sultan Kudarat State University</strong></p>
                                        <p style='margin: 0;'>This email is auto-generated by the USG Accreditation System.</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </body>
            </html>
        ";
    }
}
