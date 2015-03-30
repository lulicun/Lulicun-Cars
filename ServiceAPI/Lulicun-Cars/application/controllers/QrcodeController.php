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
		    $contactType = filter_var($contact, FILTER_VALIDATE_EMAIL)? 'email' : 'tele';

		    $folderName = date('Y-m');
			$fileName = 'lulicun_'. $contactType . '_' . md5($contact) .'.png';
			
			$qrcodeWebUrl = "http://$_SERVER[HTTP_HOST]/qrcodeimg/" . $folderName . '/' . $fileName;
			$qrcodeAbsoluteFolder = BASE_PATH . '/public/qrcodeimg/' . $folderName;
			if (!file_exists($qrcodeAbsoluteFolder)) {
				mkdir($qrcodeAbsoluteFolder, 0777);			
			}
			$qrcodeAbsoluteFilePath = $qrcodeAbsoluteFolder . '/' . $fileName;
			
			$baseDomain = (APPLICATION_ENV != 'local')? 'http://car.lulicun.com/' : 'http://192.168.1.3/';
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
    	$template = null;
    	try {
    		$this->_sendEmail($contact, $template);
    	} catch (Exception $e) {
    		$this->_helper->json(array('err' => $e->getMessage()));	
    	}
    	$this->_helper->json(array('result' => 'success'));
    }

    
    private function _sendEmail($contact, $template) {
    	$mailer = new Lulicun_Mail();
    	$response = $mailer->send($contact);
    	if (!$response) {
            throw new Zend_Controller_Action_Exception('Could not send email.', 500);
        }	
    }
}