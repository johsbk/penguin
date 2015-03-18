<?php
namespace penguin\mail;
class MailService {
	private $transport;
	function __construct($host,$port,$user=false,$password=false) {
		if (!class_exists('Swift_SmtpTransport')) {
			throw new \Exception('Swift not found');
		}
		if ($port==465) {

			$this->transport = \Swift_SmtpTransport::newInstance($host,$port,'ssl');
		} else {
			$this->transport = \Swift_SmtpTransport::newInstance($host,$port);

		}
		if ($user) {
			$this->transport->setUsername($user)->setPassword($password);
		}
	}
	function send_mail($to,$from,$subject,$body,$htmlbody=false) {
		
		$mailer = \Swift_Mailer::newInstance($this->transport);
		$message = \Swift_Message::newInstance($subject)
			->setFrom($from)
			->setTo($to)
			->setBody($body);
		if ($htmlbody) {
			$message->addPart($htmlbody,'text/html');
		}
		$result = $mailer->send($message);
	}
}