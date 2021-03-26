<?php
	$smtp = ['ssl://smtp.gmail.com', 465];
	
	function smtparse($sock) {
		$resp = '';
		while (substr($resp, 3, 1) != ' ') {
			$resp = fgets($sock, 256);
			if (!$resp)
				return false;
		}
		return $resp;
	}

	function smtparse2($str, $code) {
		return (substr($str, 0, 3) == $code);
	}
	
	$boundary = bin2hex(random_bytes(16));
	$mailb = '--'.$boundary."\r\n".'Content-Type: text/plain; charset="utf-8"'."\r\n".'Content-Transfer-Encoding: 8bit'."\r\n\r\n".$mailb0."\r\n\r\n".'--'.$boundary."\r\n".'Content-Type: text/html; charset="utf-8"'."\r\n".'Content-Transfer-Encoding: 8bit'."\r\n\r\n".$mailb."\r\n\r\n".'--'.$boundary.'--';
	$mailb = implode("\r\n", [
		'Date: '.date("D, d M Y H:i:s").' UT',
		'Subject: =?utf-8?B?'.base64_encode($maili).'?=',
		'Reply-To: '.$mailu[0][1],
		'List-Unsubscribe: '.$mailu[0][1],
		'To: <'.$mailt.'>',
		'MIME-Version: 1.0',
		//'Content-Type: text/html; charset="utf-8"',
		'Content-Type: multipart/alternative; boundary="'.$boundary.'"',
		//'Content-Transfer-Encoding: 8bit',
		'From: =?utf-8?B?'.base64_encode($mailu[0][0]).'?= <'.$mailu[1][0].'>',
		//'From: '.$mailu[0][0].' <'.$mailu[1][0].'>',
	])."\r\n\r\n".$mailb."\r\n\r\n.\r\n";

	$sock = fsockopen($smtp[0], $smtp[1]);
	if (!$sock)
		return;
	$t = smtparse($sock);
	if (!smtparse2($t, '220')) {
		$merr = $t;
		return;
	}
	fputs($sock, 'HELO '.explode('://', $smtp[0])[1]."\r\n");
	$t = smtparse($sock);
	if (!smtparse2($t, '250')) {
		$merr = $t;
		return;
	}
	fputs($sock, 'AUTH LOGIN'."\r\n");
	$t = smtparse($sock);
	if (!smtparse2($t, '334')) {
		$merr = $t;
		return;
	}
	fputs($sock, base64_encode($mailu[1][0])."\r\n");
	$t = smtparse($sock);
	if (!smtparse2($t, '334')) {
		$merr = $t;
		return;
	}
	fputs($sock, base64_encode($mailu[1][1])."\r\n");
	$t = smtparse($sock);
	if (!smtparse2($t, '235')) {
		$merr = $t;
		return;
	}
	fputs($sock, 'MAIL FROM: <'.$mailu[1][0].'>'."\r\n");
	$t = smtparse($sock);
	if (!smtparse2($t, '250')) {
		$merr = $t;
		return;
	}
	fputs($sock, 'RCPT TO: <'.$mailt.'>'."\r\n");
	$t = smtparse($sock);
	if (!smtparse2($t, '250')) {
		$merr = $t;
		return;
	}
	fputs($sock, 'DATA'."\r\n");
	$t = smtparse($sock);
	if (!smtparse2($t, '354')) {
		$merr = $t;
		return;
	}
	fputs($sock, $mailb."\r\n");
	$t = smtparse($sock);
	if (!smtparse2($t, '250')) {
		$merr = $t;
		return;
	}
	fputs($sock, 'QUIT'."\r\n");
	fclose($sock);
	
	$result = [true];
?>