<?php

use PHPMailer\PHPMailer\PHPMailer;

class NotificationLogic {

	function send_notification_pixel($input) {

		if ($input->has('/email_notification_id')) {
			$email_id = $input->get('/email_notification_id');

			$do = new EmailToSendDO($email_id);
			
			//to remove this two lines
			//$do->sended = 1;
			//$do->saveOrUpdate();

			$do->send();

			$pix = new LFile(FRAMEWORK_DIR_NAME.'/lib/images/notification_pixel.png');
			$response = new LHttpFileResponse($pix->getFullPath(),"notification_pixel.png",true,'image/png');
			$response->execute();
		} else {
			echo "Parameter is missing : email_notification_id";
			exit(1);
		}


	}

	function send_test_email() {

		try {
			$mail = new PHPMailer(true);

			$mail->setFrom("no_reply@marcobedeschi.it");

			$mail->addAddress("ml@mbcraft.it");
			
			$mail->isHTML(true);
			$mail->Subject = "This is a test email";
			$mail->Body = "<div>This is a test email body</div>";

			$mail->send();

			echo "Email sent.";
			exit(1);
		} catch (\Exception $ex) {
			echo "Exception during sending email : ".$ex->getMessage();
			$ex->printStackTrace();
			exit(1);
		}
	}
	
}