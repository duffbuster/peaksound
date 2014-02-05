<?php

// Something's broken in here!! Work with bret to fix it
require_once '/unirest-php/lib/Unirest.php';
require_once '/sendgrid-php/lib/SendGrid.php';
require_once '/swiftmailer/lib/swift_required.php';
SendGrid::register_autoloader();
$sendgrid_username = 'azure_a52dce55d5229d7ab9f48a768ca530dd@azure.com';
$sendgrid_password = '1wcxhjvy';
$sendgrid = new SendGrid($sendgrid_username, $sendgrid_password);
$transport  = Swift_SmtpTransport::newInstance('smtp.sendgrid.net', 587);
$transport->setUsername($sendgrid_username);
$transport->setPassword($sendgrid_password);

$mailer     = Swift_Mailer::newInstance($transport);
// check for form submission - if it doesn't exist then send back to contact form
if (!isset($_POST['save']) || $_POST['save'] != 'contact') {
    header('Location: contact.php'); exit;
}
	
// get the posted data
$name = $_POST['contact_name'];
$email_address = $_POST['contact_email'];
$phone = $_POST['contact_phone'];
$message = $_POST['contact_message'];
	
// check that a name was entered
if (empty($name))
    $error = 'You must enter your name.';
// check that an email address was entered
elseif (empty($email_address)) 
    $error = 'You must enter your email address.';
// check for a valid email address
elseif (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $email_address))
    $error = 'You must enter a valid email address.';
// check that a phone number was entered
if (empty($phone))
    $error = 'You must enter your phone number.';
// check that a message was entered
elseif (empty($message))
    $error = 'You must enter a message.';
		
// check if an error was found - if there was, send the user back to the form
if (isset($error)) {
    header('Location: contact.php?e='.urlencode($error)); exit;
}

$headers = "From: $email_address\r\n"; 
$headers .= "Reply-To: $email_address\r\n";

// write the email content
$email_content = "Name: $name\n";
$email_content .= "Email Address: $email_address\n";
$email_content .= "Phone Number: $phone\n";
$email_content .= "Message:\n\n$message";
	
// send the email
//ENTER YOUR INFORMATION BELOW FOR THE FORM TO WORK!
$message    = new Swift_Message();
$message->setTo('peaksoundva@gmail.com');
$message->setFrom($email_address);
$message->setSubject("PeaksoundVA - Contact Form Submission");
$message->setBody($email_content);

$header           = new Smtpapi\Header();
$header->addSubVal("%how%", array("Owl"));

$message_headers  = $message->getHeaders();
$message_headers->addTextHeader("x-smtpapi", $header->toJsonString());

$mailer->send($message);
/*$mail = new SendGrid\Email();
$mail->addTo('peaksoundva@gmail.com')->
       setFrom($email_address)->
       setSubject('PeaksoundVA - Contact Form Submission')->
       setText($email_content)->
       addMessageHeader($headers);

$response = $sendgrid->web->send($mail);
var_dump($response);*/
       //setHtml('<strong>Hello World!</strong>');
//mail ('peaksoundva@gmail.com', 'PeaksoundVA - Contact Form Submission', $email_content, $headers);
	
// send the user back to the form
header('Location: contact.html?s='.urlencode('Thank you for your message.')); exit;

?>