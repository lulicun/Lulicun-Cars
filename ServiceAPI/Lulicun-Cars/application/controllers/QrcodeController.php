<?php
require 'phpqrcode/qrlib.php';
class QrcodeController extends BaseController
{

    public function init()
    {
        /* Initialize action controller here */
        parent::init();
    }

    public function getQrcodeAction()
    {
        $contact = $_GET['contact'];
        if (!filter_var($contact, FILTER_VALIDATE_EMAIL) 
	        && !(preg_match("/^1[34578]\d{9}$/", $contact))) {
        	$this->_helper->json(array('err' => 'Bad request, neither eamil nor telephone!'));
	    }

	    $qrcode = Lulicun_QRCode::getByContact($contact);
	    if (!$qrcode || !file_exists($qrcode['filePath'])) {
		    $config = Zend_Registry::get('config');
            $baseDomain = $config->get('base_url');

            $contactType = filter_var($contact, FILTER_VALIDATE_EMAIL)? 'email' : 'tele';

		    $folderName = date('Y-m');
			$fileName = 'lulicun_'. $contactType . '_' . md5($contact) .'.png';
			
			$qrcodeWebUrl = $baseDomain . "qrcodeimg/" . $folderName . '/' . $fileName;
			$qrcodeAbsoluteFolder = BASE_PATH . '/public/qrcodeimg/' . $folderName;
			if (!file_exists($qrcodeAbsoluteFolder)) {
                //TODO: Codes to make it 777 is not working.
                $old_umask = umask(0);
				mkdir($qrcodeAbsoluteFolder, 0777, true); 
                umask($old_umask);			
			}
			$qrcodeAbsoluteFilePath = $qrcodeAbsoluteFolder . '/' . $fileName;
			
			$stringToQrcode = $baseDomain . '#/qrcodeReminder/' . $contactType . '_' . md5($contact);
			
			try{
				QRcode::png($stringToQrcode, $qrcodeAbsoluteFilePath);
			} catch(Exception $e) {
				$this->_helper->json(array('err' => $e->getMessage()));	
			}
			if (!$qrcode) {
				$generate_qrcode = array(
					'_id' => new MongoId(),
					'contact' => $contact,
					'contactType' => $contactType,
					'encryptedContact' => md5($contact),
					'webUrl' => $qrcodeWebUrl,
					'filePath' => $qrcodeAbsoluteFilePath,
					'qrcodetext' => $stringToQrcode
				);			
				$created = Lulicun_QRCode::create($generate_qrcode);
				if ($created) {
					$this->_helper->json(array('webUrl' => $qrcodeWebUrl));
		        } else {
		            $this->_helper->json(array('err' => 'Server error: failed to save to DB'));
		        }
			} else {
				$this->_helper->json(array('webUrl' => $qrcode['webUrl']));
			}			
	    } else {
	    	$this->_helper->json(array('webUrl' => $qrcode['webUrl']));
	    }
    }

    public function sendReminderAction()
    {
    	$encryptedContact = $_GET['encryptedContact'];
    	$qrcode = Lulicun_QRCode::getByEncryptedContact($encryptedContact);
    	$contact = $qrcode['contact'];
    	$firstname = '车主'; //TODO: Get the first name if the user has a account 
    	$template = 'move_car_reminder.phtml';
        $subject = 'Lulicun－挪车提醒';
    	try {
    		$this->_sendEmail($contact, $firstname, $template, $subject);
    	} catch (Exception $e) {
    		$this->_helper->json(array('err' => $e->getMessage()));	
    	}
    	$this->_helper->json(array('result' => 'success'));
    }

    
    private function _sendEmail($contact, $firstname = null, $template = 'move_car_reminder.phtml', $subject = 'Lulicun－挪车提醒') {

    	//Get basic settings of from email
    	$config = Zend_Registry::get('config');
    	$from = $config->get('email_address')->get('service');
    	$from_password = $config->get('email_password')->get('service');
    	$from_name = $config->get('email_from_name')->get('service');

    	//Prepare content to send
    	$email_template_frame = new Lulicun_EmailView();
    	$email_template_content = new Lulicun_EmailView();
    	$greeting = $firstname ? '你好 ' . ucfirst($firstname) . ',' : '';
    	$email_template_content->greeting = $greeting;
    	$email_template_frame->content = $email_template_content->render($template);
        $body = $email_template_frame->render('email_layout.phtml');

    	$email_params = array(
    		'from' => $from,
    		'password' => $from_password,
    		'name' => $from_name,
    		'to' => $contact,
    		'subject' => $subject,
    		'html' => $body,
    		'replyto' => $from,
    	);

    	$mailer = new Lulicun_Mail();
    	$response = $mailer->send($email_params);

    	if (!$response) {
            throw new Zend_Controller_Action_Exception('Could not send email.', 500);
        }	
    }
}