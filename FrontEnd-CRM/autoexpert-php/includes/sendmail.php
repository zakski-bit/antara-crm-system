<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once('phpmailer/class.phpmailer.php');
require_once('phpmailer/class.smtp.php');

$mail = new PHPMailer();

//$mail->SMTPDebug = 3;                               // Enable verbose debug output
$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'just55.justhost.com';                  // Specify SMTP server
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'themeforest@ismail-hossain.me';    // SMTP username
$mail->Password = 'AsDf12**';                         // SMTP password
$mail->SMTPSecure = 'ssl';                            // Enable SSL encryption
$mail->Port = 465;                                    // TCP port for SSL

$message = "";
$status = "false";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = isset($_POST['form_name']) ? trim($_POST['form_name']) : '';
    $email   = isset($_POST['form_email']) ? trim($_POST['form_email']) : '';
    $subject = isset($_POST['form_subject']) ? trim($_POST['form_subject']) : 'New Message | Contact Form';
    $company = isset($_POST['form_company']) ? trim($_POST['form_company']) : '';
    $phone   = isset($_POST['form_phone']) ? trim($_POST['form_phone']) : '';
    $msg     = isset($_POST['form_message']) ? trim($_POST['form_message']) : '';
    $botcheck = isset($_POST['form_botcheck']) ? $_POST['form_botcheck'] : '';

    if (!empty($name) && !empty($email) && !empty($subject)) {
        if ($botcheck === '') {
            $toemail = 'spam.thememascot@gmail.com';
            $toname  = 'ThemeMascot';

            $mail->setFrom($email, $name);
            $mail->addReplyTo($email, $name);
            $mail->addAddress($toemail, $toname);
            $mail->Subject = $subject;

            $body = "
                <strong>Name:</strong> {$name}<br><br>
                <strong>Email:</strong> {$email}<br><br>
            ";
            if (!empty($company)) {
                $body .= "<strong>Company:</strong> {$company}<br><br>";
            }
            $body .= "
                <strong>Phone:</strong> {$phone}<br><br>
                <strong>Message:</strong> {$msg}<br><br>
            ";

            if (!empty($_SERVER['HTTP_REFERER'])) {
                $body .= "<br><br>This form was submitted from: {$_SERVER['HTTP_REFERER']}";
            }

            $mail->msgHTML($body);
            $sendEmail = $mail->send();

            if ($sendEmail) {
                $message = 'We have <strong>successfully</strong> received your message and will get back to you shortly.';
                $status = "true";
            } else {
                $message = 'Email <strong>could not</strong> be sent.<br><strong>Reason:</strong> ' . $mail->ErrorInfo;
                $status = "false";
            }
        } else {
            $message = 'Bot <strong>Detected</strong>! Submission blocked.';
            $status = "false";
        }
    } else {
        $message = 'Please <strong>fill in all required</strong> fields and try again.';
        $status = "false";
    }
} else {
    $message = 'An <strong>unexpected error</strong> occurred. Please try again later.';
    $status = "false";
}

echo json_encode(['message' => $message, 'status' => $status]);
?>
