<?php


use PHPMailer\PHPMailer\PHPMailer;

class EmailToSendDO extends LAbstractDataObject {

	const MY_TABLE = "email_to_send";

	public $id;
	public $sender;
	public $rcpt_to;
	public $rcpt_bcc;
	public $text_subject;
	public $html;
	public $sended;
	public $sended_at;


	public function renderTwigTemplateToHtml($template_path,$template_vars) {

		$renderer = new LTemplateRendering();
		$renderer->setupTemplateSource('twig');

		$result = $renderer->render($template_path,$template_vars);

		$this->html = $result;

	}

	public function send() {

		if ($this->sended) return;

		$mail = new PHPMailer(true);

		$mail->setFrom($this->sender);

		if ($this->rcpt_to) {
			$to_addresses = explode(',',$this->rcpt_to);

			foreach ($to_addresses as $address) {
				$mail->addAddress($address);
			}
		}

		if ($this->rcpt_bcc) {
			$bcc_addresses = explode(',',$this->rcpt_bcc);

			foreach ($bcc_addresses as $address) {
				$mail->addBCC($address);
			}
		}

		$mail->isHTML(true);
		$mail->Subject = $this->text_subject;
		$mail->Body = $this->html;

		$mail->send();

		$this->sended = 1;
		$this->sended_at = date('Y-m-d H:i:s');
		$this->saveOrUpdate();

	}


}