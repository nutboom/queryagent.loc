<?php
/*
    $mailer = new Mailer();
    $mailer->setTemplate("/home/www/template.tpl");

    $mailer->from	=	"Support Company <support@company.com>";
	$mailer->to		=	"ivan@petrov.com";
	$mailer->subject=	"Dear client";

	$mailer->html   =	"<b>Hello!</b>";
	#$mailer->text   =	"Hello!";

	$mailer->addAttach("/home/www/document.pdf", "application/pdf");

	$mailer->send();
*/
class Mailer {
	public $subject;       # (string) Тема
	public $text;          # (string) Текст сообщения (txt-вариант)
	public $html;          # (string) Текст сообщения (html-вариант)
	public $from;          # (string) От кого
	public $to;            # (string) Кому
	public $reply;         # (string) Ответ
	public $charset = 'UTF-8'; # (string) Кодировка

	private $headers;       # (string) Заголовки письма
	private $body;          # (string) Содержимое письма
	private $template;  	# (string) HTML-шаблон письма
	private $boundary;      # (string) Разделитель для вложений
	private $attaches = array(); # (array) Массив вложений

	public function __construct() {
		$this->boundary = '----'.substr(md5(uniqid(rand(),true)),0,16);
		$this->template = '<html><head><title>{title}</title></head><body>{content}</body></html>';
	}

	# прикрепляем файл к письму
	public function addAttach($path, $mime) {
		if (file_exists($path)) {
			$name = basename($path);
			$attach = "Content-Type: $mime; name=\"$name\"\r\n";
			$attach .= "Content-Disposition: attachment; filename=\"$name\"\r\n";
			$attach .= "Content-Transfer-Encoding: base64\r\n";
			$attach .= "\r\n";
			$attach .= base64_encode(file_get_contents($path))."\r\n";
			$this->attaches[] = $attach;
		}
	}

	# устанавливаем html-шаблон письма
	public function setTemplate($path) {
		if (file_exists($path)) {
			$this->template = file_get_contents($path);
		}
	}

	# отправляем письмо
	public function send() {
		$attaches = count($this->attaches);

		$this->headers = "From: {$this->from}\r\n";
		$this->headers .= "MIME-Version: 1.0\r\n";

		if (!$this->reply) {
			$this->reply = $this->from;
		}

		$this->headers .= "Reply-To: {$this->reply}\r\n";

		$this->subject = '=?'.$this->charset.'?B?'.base64_encode($this->subject).'?=';

		# если нет вложений и html-содержимого, то это text/plain
		if (!$this->html && !$attaches) {
			$this->headers .= 'Content-Type: text/plain; charset='.$this->charset."\r\n";
			$this->body = $this->text;
		}
		# если нет вложений, но у нас html-содержимое
		elseif ($this->html && !$attaches) {
			$this->headers .= 'Content-Type: text/html; charset='.$this->charset."\r\n";
			$this->body = str_replace(array('{title}', '{content}'), array($this->subject, $this->html), $this->template);
		}
		# если есть вложения, но нет html-содержимого
		elseif (!$this->html && $attaches) {
			$this->headers .= "Content-Type: multipart/mixed; boundary=\"{$this->boundary}\"\r\n";

			$this->body .= "--{$this->boundary}\r\n";
			$this->body .= "Content-Type: text/plain; charset={$this->charset}\r\n";
			$this->body .= "Content-Transfer-Encoding: 8bit\r\n";
			$this->body .= "\r\n";

			$this->body .= $this->text;


			foreach ($this->attaches as $attach) {
				$this->body .= "--{$this->boundary}\r\n";
				$this->body .= $attach;
			}

			$this->body .= "--{$this->boundary}--\r\n";
		}
		# есть и вложения и html-содержимое
		elseif ($this->html && $attaches) {
			$this->headers .= "Content-Type: multipart/mixed; boundary=\"{$this->boundary}\"\r\n";

			$this->body .= "--{$this->boundary}\r\n";
			$this->body .= "Content-Type: text/html; charset={$this->charset}\r\n";
			$this->body .= "Content-Transfer-Encoding: 8bit\r\n";
			$this->body .= "\r\n";

			$this->body .= str_replace(array('{title}', '{content}'), array($this->subject, $this->html), $this->template);

			foreach ($this->attaches as $attach) {
				$this->body .= "--{$this->boundary}\r\n";
				$this->body .= $attach;
			}

			$this->body .= "--{$this->boundary}--\r\n";
		}

		if (!mail($this->to, $this->subject, $this->body, $this->headers)) {
			return false;
		}

		return true;
	}
}
?>