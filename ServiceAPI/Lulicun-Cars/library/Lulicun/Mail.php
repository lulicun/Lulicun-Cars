<?php
/**
 * This class handles sending email
 */

class Lulicun_Mail {

	public function send($params) {
		$token = Resque::enqueue('default', 'Application_Job_MoveCarReminder', array('params' => $params));
		$status = new Resque_Job_Status($token);
		error_log($status->get()); // Outputs the status
		return true;
	}

	public static function _send($params) {
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
		$mail->Username = $params['from'];  // SMTP username
		$mail->Password = $params['password'];                 // SMTP password
		$mail->SMTPSecure = 'ssl';                        // Enable encryption
		
		$mail->From = $params['replyto'];	  // sender Email address shown on receiver page
		$mail->FromName = $params['name'];				  // Sender name shown on receiver page
		$mail->addAddress($params['to']); 
		$mail->Subject = $params['subject'];  
		$mail->isHTML(true);  					// Subject of your Email message                       
		$mail->Body = $params['html'];;
		
		if(!$mail->send()) {
			return false;
		}
		return true;
	}
}