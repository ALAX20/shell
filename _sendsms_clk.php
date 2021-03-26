<?php
	function xReq($auth, $url, $post) {
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
		curl_setopt($curl, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json',
			'Authorization: '.$auth,
		]);
		$result = curl_exec($curl);
		curl_close($curl);
		return $result;
	}
	
	function xSend($phone, $message) {
		$auth = authSmsSend('clk');
		$post = json_encode([
			'messages' => [
				[
					'channel' => 'sms',
					'to' => $phone,
					'content' => $message,
				],
			],
		]);
		$page = xReq($auth, 'https://platform.clickatell.com/v1/message', $post);
		$page = json_decode($page, true);
		$check = $page['messages'][0]['accepted'];
		$msg = $page['messages'][0]['error']['description'];
		return [$check, $msg, 0];
	}
?>