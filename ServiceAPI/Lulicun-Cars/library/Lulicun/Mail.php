<?php
/**
 * This class handles sending email
 */

class Lulicun_Mail {

	public function send($contact) {

		error_log($contact);
		$mail = new PHPMailer;

		$mail->isSMTP();                    // Set mailer to use SMTP
		$mail->CharSet = "UTF-8";			// Set encoding 
		//$mail->SMTPDebug  = 2;           	// Enable SMTP debug, it will print information
											//	 of smtp on your page										 
                                            // 1 = errors and messages
                                            // 2 = messages only
		$mail->Host = 'smtp.googlemail.com';    		  // Specify main and backup server
		$mail->Port = 465;                   		      // SMTP Server port
		$mail->SMTPAuth = true;                           // Enable SMTP authentication
		$mail->Username = 'servicelulicun@gmail.com';  // SMTP username
		$mail->Password = 'Lulicun2015';                 // SMTP password
		$mail->SMTPSecure = 'ssl';                        // Enable encryption
		
		$mail->From = 'servicelulicun@gmail.com';	  // sender Email address shown on receiver page
		$mail->FromName = 'Lulicun';				  // Sender name shown on receiver page
		$mail->addAddress($contact); 
		$mail->Subject = 'HTML Form Test';  
		$mail->isHTML(true);  					// Subject of your Email message                       
		$mail->Body = '<h1>Lulicun Reminder</h1><b>Please move you car as soon as possible</b>';
		if(!$mail->send()) {
			return false;
		}
		return true;
	}
}