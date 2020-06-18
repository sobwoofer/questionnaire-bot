<?php 

require 'config.php';

$full_name = $_POST['m-full-name'];
$email = $_POST['m-email'];
$phone_number = $_POST['m-phone-number'];
$subject = $_POST['m-subject'];
$message = $_POST['m-message'];

$no_subject = 'Subject Not Essential';
$e_subject = (!empty($subject)) ? $subject : $no_subject;
$e_fullname = (!empty($full_name)) ? $full_name :  $email;

$e_content = "You have been contacted by ". $e_fullname .". Their additional message is as follows.<br><br>";
$e_content .= "Subject : ". $e_subject . "<br><br>";
$e_content .= "Phone Number: " . $phone_number . "<br><br>";
$e_content .= $message . "<br><br>";
$e_content .= "You can contact $e_fullname via email, $email";

$headers = "From: $email" . PHP_EOL;
$headers .= "Reply-To: $email" . PHP_EOL;
$headers .= "MIME-Version: 1.0" . PHP_EOL;
$headers .= "Content-type: text/html; charset=utf-8" . PHP_EOL;

$mail = mail(MINIMALIO_RECIPIENT_EMAIL_ADDRESS, $e_subject, $e_content, $headers);

if($mail){
	echo "Successfully sent email.";
}

?>