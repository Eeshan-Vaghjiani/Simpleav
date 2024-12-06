<?php
require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/phpmailer/src/SMTP.php';
require '../vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);

        // Configure SMTP
        $this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.gmail.com';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = 'simpleave33@gmail.com'; // Your email
        $this->mailer->Password = 'ssbb nnhq otzs cnvi'; // Your app password
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = 587;

        // Set default sender
        $this->mailer->setFrom('simpleave33@gmail.com', 'SimpLeav');
    }

    public function send2FACode($to, $code) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Your SimpLeav Verification Code';
            $this->mailer->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <h2 style='color: #333;'>Verification Code</h2>
                    <p>Your verification code is:</p>
                    <h1 style='color: #007bff; font-size: 32px; letter-spacing: 5px;'>{$code}</h1>
                    <p style='color: #666;'>This code will expire in 15 minutes.</p>
                    <p style='color: #666;'>If you didn't request this code, please ignore this email.</p>
                </div>
            ";

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Failed to send 2FA code: " . $e->getMessage());
            return false;
        }
    }

    public function sendLoginNotification($to) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'New Login to Your SimpLeav Account';
            $this->mailer->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <h2 style='color: #333;'>New Login Alert</h2>
                    <p>A new login was detected on your SimpLeav account.</p>
                    <p>Time: " . date('Y-m-d H:i:s') . "</p>
                    <p style='color: #666;'>If this wasn't you, please contact support immediately.</p>
                </div>
            ";

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Failed to send login notification: " . $e->getMessage());
            return false;
        }
    }

    public function sendLeaveApplicationConfirmation($to, $leaveDetails) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Leave Application Confirmation - SimpLeav';
            
            // Create a professional HTML email template
            $this->mailer->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>
                    <div style='text-align: center; margin-bottom: 20px;'>
                        <h1 style='color: rgba(50, 135, 214, 0.975); margin: 0;'>Leave Application Confirmation</h1>
                        <p style='color: #666; margin-top: 5px;'>Your leave request has been submitted successfully</p>
                    </div>

                    <div style='background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px;'>
                        <h2 style='color: rgba(50, 135, 214, 0.975); font-size: 18px; margin-top: 0;'>Leave Details</h2>
                        <table style='width: 100%; border-collapse: collapse;'>
                            <tr>
                                <td style='padding: 8px 0; color: #666;'>Employee Name:</td>
                                <td style='padding: 8px 0; color: #333;'>{$leaveDetails['employee_name']}</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #666;'>Department:</td>
                                <td style='padding: 8px 0; color: #333;'>{$leaveDetails['department']}</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #666;'>Leave Type:</td>
                                <td style='padding: 8px 0; color: #333;'>{$leaveDetails['leave_type']}</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #666;'>Start Date:</td>
                                <td style='padding: 8px 0; color: #333;'>{$leaveDetails['start_date']}</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #666;'>End Date:</td>
                                <td style='padding: 8px 0; color: #333;'>{$leaveDetails['end_date']}</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #666;'>Duration:</td>
                                <td style='padding: 8px 0; color: #333;'>{$leaveDetails['duration']} days</td>
                            </tr>
                        </table>
                    </div>

                    <div style='background-color: #f9f9f9; padding: 15px; border-radius: 5px;'>
                        <h2 style='color: rgba(50, 135, 214, 0.975); font-size: 18px; margin-top: 0;'>Reason for Leave</h2>
                        <p style='color: #333; margin: 0;'>{$leaveDetails['reason']}</p>
                    </div>

                    <div style='margin-top: 20px; padding: 15px; background-color: #f0f7ff; border-radius: 5px;'>
                        <p style='color: #666; margin: 0; font-size: 14px;'>
                            Your leave application has been submitted and is pending approval. You will be notified once it has been reviewed.
                        </p>
                    </div>

                    <div style='margin-top: 20px; text-align: center; color: #666; font-size: 12px;'>
                        <p>This is an automated message from SimpLeav. Please do not reply to this email.</p>
                    </div>
                </div>
            ";

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Failed to send leave application confirmation: " . $e->getMessage());
            return false;
        }
    }

    public function sendLeaveStatusUpdate($to, $leaveDetails, $status) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            $this->mailer->isHTML(true);
            
            // Set subject based on status
            $statusText = $status === 'approved' ? 'Approved' : 'Rejected';
            $statusColor = $status === 'approved' ? '#28a745' : '#dc3545';
            $this->mailer->Subject = "Leave Application {$statusText} - SimpLeav";

            // Create professional HTML email template
            $this->mailer->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>
                    <!-- Header -->
                    <div style='text-align: center; margin-bottom: 20px;'>
                        <h1 style='color: rgba(50, 135, 214, 0.975); margin: 0;'>Leave Application Update</h1>
                        <div style='background-color: {$statusColor}; color: white; padding: 10px; border-radius: 5px; margin-top: 10px;'>
                            <h2 style='margin: 0; font-size: 24px;'>Your leave has been {$statusText}</h2>
                        </div>
                    </div>

                    <!-- Application Details -->
                    <div style='background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px;'>
                        <h2 style='color: rgba(50, 135, 214, 0.975); font-size: 18px; margin-top: 0;'>Leave Details</h2>
                        <table style='width: 100%; border-collapse: collapse;'>
                            <tr>
                                <td style='padding: 8px 0; color: #666;'>Employee Name:</td>
                                <td style='padding: 8px 0; color: #333;'>{$leaveDetails['employee_name']}</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #666;'>Department:</td>
                                <td style='padding: 8px 0; color: #333;'>{$leaveDetails['department']}</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #666;'>Leave Type:</td>
                                <td style='padding: 8px 0; color: #333;'>{$leaveDetails['leave_type']}</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #666;'>Duration:</td>
                                <td style='padding: 8px 0; color: #333;'>{$leaveDetails['start_date']} to {$leaveDetails['end_date']}</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #666;'>Total Days:</td>
                                <td style='padding: 8px 0; color: #333;'>{$leaveDetails['duration']} days</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Approval Details -->
                    <div style='background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px;'>
                        <h2 style='color: rgba(50, 135, 214, 0.975); font-size: 18px; margin-top: 0;'>Review Details</h2>
                        <table style='width: 100%; border-collapse: collapse;'>
                            <tr>
                                <td style='padding: 8px 0; color: #666;'>Reviewed By:</td>
                                <td style='padding: 8px 0; color: #333;'>{$leaveDetails['manager_name']}</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #666;'>Review Date:</td>
                                <td style='padding: 8px 0; color: #333;'>{$leaveDetails['review_date']}</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #666;'>Review Time:</td>
                                <td style='padding: 8px 0; color: #333;'>{$leaveDetails['review_time']}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Manager's Notes -->
                    <div style='background-color: #f9f9f9; padding: 15px; border-radius: 5px;'>
                        <h2 style='color: rgba(50, 135, 214, 0.975); font-size: 18px; margin-top: 0;'>Manager's Notes</h2>
                        <p style='color: #333; margin: 0;'>" . 
                        (!empty($leaveDetails['notes']) ? $leaveDetails['notes'] : 'No additional notes provided.') . 
                        "</p>
                    </div>

                    <!-- Next Steps -->
                    <div style='margin-top: 20px; padding: 15px; background-color: #f0f7ff; border-radius: 5px;'>";
            
            if ($status === 'approved') {
                $this->mailer->Body .= "
                        <p style='color: #666; margin: 0;'>
                            Your leave has been approved. Please ensure proper handover of your responsibilities before your leave period.
                        </p>";
            } else {
                $this->mailer->Body .= "
                        <p style='color: #666; margin: 0;'>
                            Your leave has been rejected. Please contact your manager for more information if needed.
                        </p>";
            }

            $this->mailer->Body .= "
                    </div>

                    <!-- Footer -->
                    <div style='margin-top: 20px; text-align: center; color: #666; font-size: 12px;'>
                        <p>This is an automated message from SimpLeav. Please do not reply to this email.</p>
                        <p>If you have any questions, please contact your manager or HR department.</p>
                    </div>
                </div>
            ";

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log("Failed to send leave status update: " . $e->getMessage());
            return false;
        }
    }
}
