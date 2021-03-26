<?php
	function xReq($key, $query) {
		$curl = curl_init('https://smshub.org/stubs/handler_api.php?api_key='.$key.'&action='.$query);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($curl);
		curl_close($curl);
		return $result;
	}
	
	function xStatus() {
		$key = authSmsRecv('shb');
		$page = json_decode(xReq($key, 'getNumbersStatus&country=0'), true);
		return [$page['ua_0'], $page['av_0'], $page['ym_0'], $page['wa_0']];
	}

	function xNumber($srvc0) {
		$key = authSmsRecv('shb');
		$page = explode(':', xReq($key, 'getNumber&service='.$srvc0.'&country=0'));
		if ($page[0] == 'NO_NUMBERS')
			return [false, 'Нет номеров для данного сервиса'];
		if ($page[0] == 'NO_BALANCE')
			return [false, 'Нет денег для покупки номера'];
		if ($page[0] != 'ACCESS_NUMBER')
			return [false, 'Неизвестная ошибка, попробуйте позже'];
		$balance = explode(':', xReq($key, 'getBalance'))[1];
		return [true, $page[1], $page[2], intval($balance)];
	}

	function xCode($id0) {
		$key = authSmsRecv('shb');
		$page = explode(':', xReq($key, 'getStatus&id='.$id0));
		if ($page[0] == 'STATUS_CANCEL')
			return [false, 'Активация отменена'];
		if ($page[0] != 'STATUS_OK' && $page[0] != 'STATUS_WAIT_RETRY')
			return [true, 'Ожидаем прихода СМС'];
		xReq($key, 'setStatus&status=3&id='.$id0);
		return [true, 'Получен код '.$page[1]];
	}
	
	function xCancel($id0) {
		$key = authSmsRecv('shb');
		xReq($key, 'setStatus&status=8&id='.$id0);
	}
?>