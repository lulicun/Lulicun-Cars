<?php

class Application_Job_MoveCarReminder {

	public function perform() {
		$result = Lulicun_Mail::_send($this->args['params']);
		error_log("jobs....");
		if ($result) {
            // all good, return
            return;
        } else {
            throw new Exception('Could not send mail.');
        }
	}
}

