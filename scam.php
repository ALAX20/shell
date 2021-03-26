<?php
	include '_set.php';
	
	$post = file_get_contents('php://input');
	if (strlen($post) < 8)
		loadSite();
	$post = json_decode($post, true);
	$msg = $post['message'];
	$iskbd = !$msg;
	if ($iskbd)
		$msg = $post['callback_query'];
	$id = beaText(strval($msg['from']['id']), chsNum());
	if (strlen($id) == 0)
		exit();
	if (isUserBanned($id))
		exit();
	$timer = 1 - (time() - intval(getUserData($id, 'time')));
	if ($timer > 0)
		exit();
	setUserData($id, 'time', time());
	$text = $msg[$iskbd ? 'data' : 'text'];
	$login = $msg['from']['username'];
	$nick = htmlspecialchars($msg['from']['first_name'].' '.$msg['from']['last_name']);
	if ($iskbd)
		$msg = $msg['message'];
	$mid = $msg[$iskbd ? 'message_id' : 'id'];
	$chat = $msg['chat']['id'];
	$image = $msg['photo'][0]['file_id'];
	$member = $msg['new_chat_member'];
	$cmd = explode(' ', $text, 2);
	$keybd = false;
	$result = false;
	$edit = false;
	$btns = [
		'profile' => '🔥 Мой профиль',
		'settings' => '🆘 Помощь',
		'manuallll' => '🪐 Мануалы',
		'myitems' => '🧾 Мои ссылки',
		'additem' => '🔗 Создать ссылку',
		'sndmail' => '✉️ Письма',
		'menusms' => '📱 SMS',
		'addsavito' => '🎁 Olx Ua',
		'addsyoula' => '🛍 Юла',
		'addssber' => '🏦 Банки',
		'addsitem' => '📦 Объявление',
		'addscars' => '🚖 Поездки',
		'addsparse' => '📝 Парсер OLX/Юла',
		'addstrack' => '🔖 Трек номер',
		'back' => '⬅️ Назад',
		'smsavito' => '🎁 Авито',
		'smsyoula' => '🛍 Юла',
        'addsrent' => '🏠 Аренда',
		'smswhats' => '👥 Whatsapp',
		'smsbbc' => '🚖 BlaBlaCar',
		'emlavito' => '🎁 Авито',
		'emlyoula' => '🛍 Юла',
		'emldrom' => '🚙 Дром',
		'emlauto' => '🚗 Авто',
		'emlbbc' => '🚖 BlaBlaCar',
		'emlsber' => '🏫 Сбербанк',
		'emlalfa' => '🏛 Альфабанк',
		'emlolxua' => '🇺🇦 OLX UA',
		'emldhl' => '🚙 DHL',
		'emlpony' => '🚛 PonyExpress',
		'emlbxbry' => '🚚 Boxberry',
		'emlkufar' => '🚚 Куфар',
		'emlbelpost' => '🚚 Белпочта',
		'emldstvs' => '📦 Dostavista',	
		'emlrent1' => '🏚 Авито',
		'emlrent2' => '🏚 Циан',
		'emlrent3' => '🏚 Юла',
		'emlcdek' => '🚛 СДЭК',
		'emlpochta' => '🗳 Почта',
		'emlpecom' => '✈️ ПЭК',
		'emlyandx' => '🚕 Яндекс',
		'emltordr' => '💸 Оплата',
		'emltrfnd' => '💫 Возврат',
		'emltsafd' => '🔒 Безоп. сделка',
		'emltcshb' => '💳 Получ. средств',
		'stgcard' => '💳 Карта',
		'pflbout' => '📤 Вывод',
		'pflhist' => '📋 История',
		'pflchck' => '🍫 Чек',
		'pflprfs' => '💰 Профиты',
		'outyes' => '✅ Подтвердить',
		'outno' => '❌ Отказаться',
		'itmdel' => '🗑 Удалить',
		'itmst1' => '⏳ Ожидает',
		'itmst2' => '🤟 Оплачен',
		'itmst3' => '💫 Возврат',
		'itmst4' => '💳 Получение',
		'itmedtnm' => '🏷 Название',
		'itmedtam' => '💸 Стоимость',
		'stgano1' => '🌕 Ник',
		'stgano0' => '🌑 Ник',
		'stgfsav' => '🎧 Фейк скриншоты поддержки',
		'stgrules' => '📜 Правила',
		'stgrefi' => '🤝 Реф. система',
		'stgchks' => '🍫 Мои чеки',
		'stgdoms' => '🌐 Домены',
		'adgoto1' => '📦 Перейти к объявлению',
		'adgoto2' => '🔖 Перейти к трек номеру',
		'stglchat' => '💎 Чат воркеров',
		'stglpays' => '💸 Выплаты',
		'outaccpt' => '📤 Выплатить',
		'jncreate' => '📝 Подать заявку',
		'jniread' => '✅ Ознакомлен',
		'jnremake' => '♻️ Заново',
		'jnsend' => '✅ Отправить',
		'jnofor' => 'Форум',
		'jnoads' => 'Реклама',
		'jnoref' => 'Друзья',
		'jnnoref' => '🌱 Никто',
		'joinaccpt' => '✅ Принять',
		'joindecl' => '❌ Отказать',
		'topshw1' => '💸 По общей сумме профитов',
		'topshw2' => '🤝 По профиту от рефералов',
		'smsrecv' => '🔑 Активация',
		'smssend' => '📩 Отправка',
		'smscode' => '♻️ Обновить',
		'smscncl' => '❌ Отменить',
		'qrcode' => '♻️',
	];
	function doSms($t, $t1, $t2) {
		global $id, $btns;
		$result = [
			'✅ <b>Номер получен</b>',
			'',
			'🏆 ID: <b>'.$t1.'</b>',
			'📞 Телефон: <b>'.$t2.'</b>',
			'☁️ Статус: <b>'.$t[1].'</b>',
			'',
			'⏱ Время обновления: <b>'.date('H:i:s').'</b>',
		];
		$keybd = false;
		if ($t[0]) {
			$keybd = [true, [
				[
					['text' => $btns['smscode'], 'callback_data' => '/smsrcvcode '.$t1.' '.$t2],
					['text' => $btns['smscncl'], 'callback_data' => '/smsrcvcncl '.$t1.' '.$t2],
				],
			]];
		}
		return [$result, $keybd];
	}
	function doDomain($t) {
		global $id, $btns;
		$srvc = intval($t);
		if ($srvc < 1 || $srvc > 20)
			return;
		$result = [
			'🌐 Выберите домен для сервиса <b>'.getService($srvc).':</b>',
		];
		$keybd = [];
		$doms = getDomains($srvc);
		$mydom = getUserDomain($id, $srvc);
		for ($i = 0; $i < count($doms); $i++) {
			$dom = $doms[$i];
			$keybd[] = [
				['text' => ($mydom == $i ? '🌟 ' : '').$dom, 'callback_data' => '/setdomain '.$srvc.' '.$i],
			];
		}
		$keybd = [true, $keybd];
		return [$result, $keybd];
	}
	function doRules() {
		return getRules();
	}
	function doShowrf($cmd2) {
		global $id, $btns;
		$t = explode(' ', $cmd2);
		if (!in_array($t[0], ['item']))
			return;
		$isnt = (int)($t[0] == 'item');
		$item = $t[1];
		if (!isItem($item, $isnt))
			return;
		if (!isUserItem($id, $item, $isnt))
			return;
		$itemd = getItemData($item, $isnt);
		$result = false;
		$keybd = false;
	    	if ($isnt == 1) {
			$result = [
				'📒 <b>Держи ссылку</b>',
				'',
				'📕 Авито: <b><a href="'.getFakeUrl($id, $item, 1, 1).'">Доставка</a></b> / <b><a href="'.getFakeUrl($id, $item, 1, 2).'">Возврат</a></b> / <b><a href="'.getFakeUrl($id, $item, 1, 4).'">Получ. средств</a></b>',

				'📗 Юла: <b><a href="'.getFakeUrl($id, $item, 2, 1).'">Доставка</a></b> / <b><a href="'.getFakeUrl($id, $item, 2, 2).'">Возврат</a></b> / <b><a href="'.getFakeUrl($id, $item, 2, 4).'">Получ. средств</a></b>',
			];
			$keybd = [true, [
				[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
				],
				[
					['text' => $btns['itmedtnm'], 'callback_data' => '/doedtnm '.$t[0].' '.$item],
					['text' => $btns['itmedtam'], 'callback_data' => '/doedtam '.$t[0].' '.$item],
				],
				/*[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
				],*/
			]];
		}
		return [$result, $keybd];
	}
	function doShowua($cmd2) {
		global $id, $btns;
		$t = explode(' ', $cmd2);
		if (!in_array($t[0], ['item']))
			return;
		$isnt = (int)($t[0] == 'item');
		$item = $t[1];
		if (!isItem($item, $isnt))
			return;
		if (!isUserItem($id, $item, $isnt))
			return;
		$itemd = getItemData($item, $isnt);
		$result = false;
		$keybd = false;
	    	if ($isnt == 1) {
			$result = [
				'📒 <b>Держи ссылку</b>',
				'',
 				'🇺🇦 OLX UA: <b><a href="'.getFakeUrl($id, $item, 21, 1).'">Доставка</a></b> / <b><a href="'.getFakeUrl($id, $item, 21, 2).'">Возврат</a></b> / <b><a href="'.getFakeUrl($id, $item, 21, 4).'">Получ. средств</a></b>',

			];
			$keybd = [true, [
				[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
				],
				[
					['text' => $btns['itmedtnm'], 'callback_data' => '/doedtnm '.$t[0].' '.$item],
					['text' => $btns['itmedtam'], 'callback_data' => '/doedtam '.$t[0].' '.$item],
				],
				/*[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
				],*/
			]];
		}
		return [$result, $keybd];
	}
	function doShowbel($cmd2) {
		global $id, $btns;
		$t = explode(' ', $cmd2);
		if (!in_array($t[0], ['item']))
			return;
		$isnt = (int)($t[0] == 'item');
		$item = $t[1];
		if (!isItem($item, $isnt))
			return;
		if (!isUserItem($id, $item, $isnt))
			return;
		$itemd = getItemData($item, $isnt);
		$result = false;
		$keybd = false;
	    	if ($isnt == 1) {
			$result = [
				'📒 <b>Держи ссылку</b>',
				'',
			
 				'📘 Куфар: <b><a href="'.getFakeUrl($id, $item, 14, 1).'">Доставка</a></b> / <b><a href="'.getFakeUrl($id, $item, 14, 2).'">Возврат</a></b> / <b><a href="'.getFakeUrl($id, $item, 14, 4).'">Получ. средств</a></b>',

				'📔 Белпочта: <b><a href="'.getFakeUrl($id, $item, 15, 1).'">Доставка</a></b> / <b><a href="'.getFakeUrl($id, $item, 15, 2).'">Возврат</a></b> / <b><a href="'.getFakeUrl($id, $item, 15, 4).'">Получ. средств</a></b>',

			];
			$keybd = [true, [
				[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
				],
				[
					['text' => $btns['itmedtnm'], 'callback_data' => '/doedtnm '.$t[0].' '.$item],
					['text' => $btns['itmedtam'], 'callback_data' => '/doedtam '.$t[0].' '.$item],
				],
				/*[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
				],*/
			]];
		}
		return [$result, $keybd];
	}
	function doShowro($cmd2) {
		global $id, $btns;
		$t = explode(' ', $cmd2);
		if (!in_array($t[0], ['item']))
			return;
		$isnt = (int)($t[0] == 'item');
		$item = $t[1];
		if (!isItem($item, $isnt))
			return;
		if (!isUserItem($id, $item, $isnt))
			return;
		$itemd = getItemData($item, $isnt);
		$result = false;
		$keybd = false;
	    	if ($isnt == 1) {
			$result = [
				'📒 <b>Держи ссылку</b>',
				'',
				'🇷🇴 OLX RO: <b><a href="'.getFakeUrl($id, $item, 24, 1).'">Доставка</a></b> / <b><a href="'.getFakeUrl($id, $item, 24, 2).'">Возврат</a></b> / <b><a href="'.getFakeUrl($id, $item, 24, 4).'">Получение</a></b>',
			];
			$keybd = [true, [
				[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
				],
				[
					['text' => $btns['itmedtnm'], 'callback_data' => '/doedtnm '.$t[0].' '.$item],
					['text' => $btns['itmedtam'], 'callback_data' => '/doedtam '.$t[0].' '.$item],
				],
				/*[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
				],*/
			]];
		}
		return [$result, $keybd];
	}
	function doShowpl($cmd2) {
		global $id, $btns;
		$t = explode(' ', $cmd2);
		if (!in_array($t[0], ['item']))
			return;
		$isnt = (int)($t[0] == 'item');
		$item = $t[1];
		if (!isItem($item, $isnt))
			return;
		if (!isUserItem($id, $item, $isnt))
			return;
		$itemd = getItemData($item, $isnt);
		$result = false;
		$keybd = false;
	    	if ($isnt == 1) {
			$result = [
				'📒 <b>Держи ссылку</b>',
				'',
				'🇵🇱 OLX PL: <b><a href="'.getFakeUrl($id, $item, 22, 1).'">Доставка</a></b> / <b><a href="'.getFakeUrl($id, $item, 22, 2).'">Возврат</a></b> / <b><a href="'.getFakeUrl($id, $item, 22, 4).'">Получение</a></b>',
			];
			$keybd = [true, [
				[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
				],
				[
					['text' => $btns['itmedtnm'], 'callback_data' => '/doedtnm '.$t[0].' '.$item],
					['text' => $btns['itmedtam'], 'callback_data' => '/doedtam '.$t[0].' '.$item],
				],
				/*[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
				],*/
			]];
		}
		return [$result, $keybd];
	}
	function doShowchex($cmd2) {
		global $id, $btns;
		$t = explode(' ', $cmd2);
		if (!in_array($t[0], ['item']))
			return;
		$isnt = (int)($t[0] == 'item');
		$item = $t[1];
		if (!isItem($item, $isnt))
			return;
		if (!isUserItem($id, $item, $isnt))
			return;
		$itemd = getItemData($item, $isnt);
		$result = false;
		$keybd = false;
	    	if ($isnt == 1) {
			$result = [
				'📒 <b>Держи ссылку</b>',
				'',
				'🇨🇿 Bazos: <b><a href="'.getFakeUrl($id, $item, 25, 1).'">Доставка</a></b> / <b><a href="'.getFakeUrl($id, $item, 25, 2).'">Возврат</a></b>',
				'🇨🇿 CBazar: <b><a href="'.getFakeUrl($id, $item, 26, 1).'">Доставка</a></b> / <b><a href="'.getFakeUrl($id, $item, 26, 2).'">Возврат</a></b> / <b><a href="'.getFakeUrl($id, $item, 26, 4).'">Получение</a></b>',
			];
			$keybd = [true, [
				[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
				],
				[
					['text' => $btns['itmedtnm'], 'callback_data' => '/doedtnm '.$t[0].' '.$item],
					['text' => $btns['itmedtam'], 'callback_data' => '/doedtam '.$t[0].' '.$item],
				],
				/*[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
				],*/
			]];
		}
		return [$result, $keybd];
	}
	function doShowport($cmd2) {
		global $id, $btns;
		$t = explode(' ', $cmd2);
		if (!in_array($t[0], ['item']))
			return;
		$isnt = (int)($t[0] == 'item');
		$item = $t[1];
		if (!isItem($item, $isnt))
			return;
		if (!isUserItem($id, $item, $isnt))
			return;
		$itemd = getItemData($item, $isnt);
		$result = false;
		$keybd = false;
	    	if ($isnt == 1) {
			$result = [
				'📒 <b>Держи ссылку</b>',
				'',
				'🇵🇹 OLX Португалия: <b><a href="'.getFakeUrl($id, $item, 27, 1).'">Доставка</a></b> / <b><a href="'.getFakeUrl($id, $item, 27, 2).'">Возврат</a></b> / <b><a href="'.getFakeUrl($id, $item, 27, 4).'">Получение</a></b>',
			];
			$keybd = [true, [
				[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
				],
				[
					['text' => $btns['itmedtnm'], 'callback_data' => '/doedtnm '.$t[0].' '.$item],
					['text' => $btns['itmedtam'], 'callback_data' => '/doedtam '.$t[0].' '.$item],
				],
				/*[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
				],*/
			]];
		}
		return [$result, $keybd];
	}
	function doShowblg($cmd2) {
		global $id, $btns;
		$t = explode(' ', $cmd2);
		if (!in_array($t[0], ['item']))
			return;
		$isnt = (int)($t[0] == 'item');
		$item = $t[1];
		if (!isItem($item, $isnt))
			return;
		if (!isUserItem($id, $item, $isnt))
			return;
		$itemd = getItemData($item, $isnt);
		$result = false;
		$keybd = false;
	    	if ($isnt == 1) {
			$result = [
				'📒 <b>Держи ссылку</b>',
				'',
				'🇧🇬 OLX Болгария: <b><a href="'.getFakeUrl($id, $item, 28, 1).'">Доставка</a></b> / <b><a href="'.getFakeUrl($id, $item, 28, 2).'">Возврат</a></b> / <b><a href="'.getFakeUrl($id, $item, 28, 4).'">Получение</a></b>',
			];
			$keybd = [true, [
				[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
				],
				[
					['text' => $btns['itmedtnm'], 'callback_data' => '/doedtnm '.$t[0].' '.$item],
					['text' => $btns['itmedtam'], 'callback_data' => '/doedtam '.$t[0].' '.$item],
				],
				/*[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
				],*/
			]];
		}
		return [$result, $keybd];
	}
	function doShow($cmd2) {
		global $id, $btns;
		$t = explode(' ', $cmd2);
		if (!in_array($t[0], ['item', 'track', 'rent','cars', 'sber']))
			return;
		$isnt = (int)($t[0] == 'item');
		if ($t[0] == 'rent') $isnt = 2;
		if ($t[0] == 'cars') $isnt = 3;
		if ($t[0] == 'sber') $isnt = 4;
		$item = $t[1];
		if (!isItem($item, $isnt))
			return;
		if (!isUserItem($id, $item, $isnt))
			return;
		$itemd = getItemData($item, $isnt);
		$result = false;
		$keybd = false;
		if ($isnt == 2) {
			$result = [
				'📒 <b>Информация об объявлении аренде</b>',
				'',
				'🏆 ID объявления: <b>'.$item.'</b>',
				'✏️ Название: <b>'.$itemd[6].'</b>',
				'💵 Стоимость: <b>'.beaCash($itemd[5]).'</b>',
				'🔍 Местоположение: <b>'.$itemd[9].'</b>',
				'',
				'🚸 Просмотров: <b>'.$itemd[0].'</b>',
				'⚠️ Профитов: <b>'.$itemd[1].'</b>',
				'⚜️ Сумма профитов: <b>'.beaCash($itemd[2]).'</b>',
				'🔱 Дата генерации: <b>'.date('d.m.Y</b> в <b>H:i', $itemd[4]).'</b>',
				'',
				'🏠 Авито: <b><a href="'.getFakeUrl($id, $item, 9, 1).'">Оплата</a></b> / <b><a href="'.getFakeUrl($id, $item, 9, 2).'">Возврат</a></b> / <b><a href="'.getFakeUrl($id, $item, 9, 3).'">Получение</a></b>',
				'🏘 Юла: <b><a href="'.getFakeUrl($id, $item, 13, 1).'">Оплата</a></b> / <b><a href="'.getFakeUrl($id, $item, 13, 2).'">Возврат</a></b> / <b><a href="'.getFakeUrl($id, $item, 13, 3).'">Получение</a></b>',
				'🏡 Циан: <b><a href="'.getFakeUrl($id, $item, 12, 1).'">Оплата</a></b> / <b><a href="'.getFakeUrl($id, $item, 12, 2).'">Возврат</a></b> / <b><a href="'.getFakeUrl($id, $item, 12, 3).'">Получение</a></b>',				

			];
			$keybd = [true, [
				[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
					// ['text' => $btns['itmedtnm'], 'callback_data' => '/doedtnm '.$t[0].' '.$item],
					// ['text' => $btns['itmedtam'], 'callback_data' => '/doedtam '.$t[0].' '.$item],
				],
				/*[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
				],*/
			]];
			} 
				else if ($isnt == 3) {
			$result = [
				'ℹ️ <b>Информация об объявлении поездки</b>',
				'',
				'🆔 ID объявления: <b>'.$item.'</b>',
				'🏷 Название: <b>'.$itemd[13].'-'.$itemd[7].'</b>',
				'💵 Стоимость: <b>'.beaCash($itemd[5]).'</b>',
				'',
				'👁 Просмотров: <b>'.$itemd[0].'</b>',
				'📊 Профитов: <b>'.$itemd[1].'</b>',
				'💰 Сумма профитов: <b>'.beaCash($itemd[2]).'</b>',
				'📅 Дата генерации: <b>'.date('d.m.Y</b> в <b>H:i', $itemd[4]).'</b>',
				'',
				'🚕 BlaBlaCar: <b><a href="'.getFakeUrl($id, $item, 16, 1).'">Оплата</a></b> / <b><a href="'.getFakeUrl($id, $item, 16, 2).'">Возврат</a></b> / <b><a href="'.getFakeUrl($id, $item, 16, 3).'">Получение</a></b>'

			];
 			$keybd = [true, [
 				[
 					    ['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
 					// ['text' => $btns['itmedtnm'], 'callback_data' => '/doedtnm '.$t[0].' '.$item],
 					// ['text' => $btns['itmedtam'], 'callback_data' => '/doedtam '.$t[0].' '.$item],
 				],
 				/*[
 					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
 				],*/
 			]];
		}
		    else if ($isnt == 4) {
			$result = [
				'📒 <b>Информация об объявлении банков</b>',
				'',
				'💫 ID: объявления: <b>'.$item.'</b>',
				'💵 Стоимость: <b>'.beaCash($itemd[5]).'</b>',
				'',
				'🚸 Просмотров: <b>'.$itemd[0].'</b>',
				'⚠️ Профитов: <b>'.$itemd[1].'</b>',
				'⚜️ Сумма профитов: <b>'.beaCash($itemd[2]).'</b>',
				'🔱 Дата генерации: <b>'.date('d.m.Y</b> в <b>H:i', $itemd[4]).'</b>',
				'',
				'🏫 Сбербанк: <b><a href="'.getFakeUrl($id, $item, 17, 5).'">Ссылка на чек</a></b>',
				'🏛 Альфабанк: <b><a href="'.getFakeUrl($id, $item, 18, 5).'">Ссылка на чек</a></b>',
			];
			$keybd = [true, [
				[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
				],
				/*[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
				],*/
			]];
		}
	    	else if ($isnt == 1) {
			$result = [
				'📒 <b>Информация об объявлении</b>',
			];
			$keybd = [true, [
				[
					['text' => '🇷🇺 Россия', 'callback_data' => '/itemrf item '.$item],
					['text' => '🇧🇾 Беларусь', 'callback_data' => '/itembel item '.$item],
				],
				[
					['text' => '🇺🇦 Украина', 'callback_data' => '/itemua item '.$item],
					['text' => '🇵🇱 Польша', 'callback_data' => '/itempl item '.$item],
				],
				[
					['text' => '🇷🇴 Румыния', 'callback_data' => '/itemro item '.$item],
					['text' => '🇨🇿 Чехия', 'callback_data' => '/itemchex item '.$item],
				],
				[
					['text' => '🇵🇹 Португалия', 'callback_data' => '/itemptl item '.$item],
					['text' => '🇧🇬 Болгария', 'callback_data' => '/itembg item '.$item],
				],
				[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
				],
				[
					['text' => $btns['itmedtnm'], 'callback_data' => '/doedtnm '.$t[0].' '.$item],
					['text' => $btns['itmedtam'], 'callback_data' => '/doedtam '.$t[0].' '.$item],
				],
				/*[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
				],*/
			]];
		} else {
			$result = [
				'📒 <b>Информация о трек номере</b>',
				'',
				'🏆 Трек номер: <b>'.$item.'</b>',
				'✏️ Название: <b>'.$itemd[6].'</b>',
				'💵 Стоимость: <b>'.beaCash($itemd[5]).'</b>',
				'🙈 От: <b>'.$itemd[9].'</b>, <b>'.$itemd[7].'</b>',
				'🔍 Кому: <b>'.$itemd[10].'</b>, <b>'.$itemd[11].'</b>',
				'⏱ Сроки доставки: <b>'.$itemd[14].'</b> - <b>'.$itemd[15].'</b>',
				'☁️ Статус: <b>'.trackStatus($itemd[16]).'</b>',
				'',
				'🚸 Просмотров: <b>'.$itemd[0].'</b>',
				'⚠️ Профитов: <b>'.$itemd[1].'</b>',
				'⚜️ Сумма профитов: <b>'.beaCash($itemd[2]).'</b>',
				'🔱 Дата генерации: <b>'.date('d.m.Y</b> в <b>H:i', $itemd[4]).'</b>',
				'',
				'🚚 Boxberry: <b><a href="'.getFakeUrl($id, $item, 3, 1).'">Отслеживание</a></b>',
				'🚛 СДЭК: <b><a href="'.getFakeUrl($id, $item, 4, 1).'">Отслеживание</a></b>',
				'🗳 Почта России: <b><a href="'.getFakeUrl($id, $item, 5, 1).'">Отслеживание</a></b>',
				'✈️ ПЭК: <b><a href="'.getFakeUrl($id, $item, 6, 1).'">Отслеживание</a></b>',
				'🚕 Яндекс: <b><a href="'.getFakeUrl($id, $item, 7, 1).'">Отслеживание</a></b>',
				'📦 Dostavista: <b><a href="'.getFakeUrl($id, $item, 8, 1).'">Отслеживание</a></b>',
				'🚐 Ponyexpress: <b><a href="'.getFakeUrl($id, $item, 10, 1).'">Отслеживание</a></b>',
				'🚌 DHL: <b><a href="'.getFakeUrl($id, $item, 11, 1).'">Отслеживание</a></b>',
			];
			$t2 = [];
			for ($i = 1; $i <= 4; $i++) {
				if ($itemd[16] != $i)
					$t2[] = ['text' => $btns['itmst'.$i], 'callback_data' => '/dostatus '.$item.' '.$i];
			}
			$keybd = [true, [
				$t2,
				[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
				],
				[
					['text' => $btns['itmedtnm'], 'callback_data' => '/doedtnm '.$t[0].' '.$item],
					['text' => $btns['itmedtam'], 'callback_data' => '/doedtam '.$t[0].' '.$item],
				],
				/*[
					['text' => $btns['itmdel'], 'callback_data' => '/dodelete '.$t[0].' '.$item],
				],*/
			]];
		}
		return [$result, $keybd];
	}
	function doSettings() {
		global $id, $btns;
		setInput($id, '');
		$result = [
			'🔑 <b>Инструменты</b>',
			'',
			'🙆🏻‍♀️ Ваш логин: <b>'.userLogin2($id).'</b>',
			'🐣 Пригласил: <b>'.getUserReferalName($id).'</b>',
		];
		$anon = (isUserAnon($id) ? '0' : '1');
		$t = [
			[
				['text' => $btns['stgdoms'], 'callback_data' => '/getdomains'],
			],
			[
				['text' => $btns['stgano'.$anon], 'callback_data' => '/setanon '.$anon],
				['text' => $btns['stgcard'], 'callback_data' => '/getcard'],
			],
			[
				['text' => $btns['stgrefi'], 'callback_data' => '/getrefi'],
				['text' => $btns['stgrules'], 'callback_data' => '/getrules'],
			],
			[
				['text' => $btns['stglchat'], 'url' => linkChat()],
				['text' => $btns['stglpays'], 'url' => linkPays()],
			],
// 			[
// 				['text' => $btns['stgfsav'], 'callback_data' => '/getscreens'],
// 			],
			[
			    ['text' => $btns['manuallll'], 'callback_data' => '/getmanuallll'],
			],
		];
		$checks = getUserChecks($id);
		$c = count($checks);
		if ($c != 0) {
			$t[0] = array_merge([
				['text' => $btns['stgchks'].' ('.$c.')', 'callback_data' => '/getchecks'],
			], $t[0]);
		}
		$keybd = [true, $t];
		return [$result, $keybd];
	}
	function doProfile() {
		global $id, $btns, $chat, $login, $text;
		$result = false;
		$json = json_decode(file_get_contents('services.json'), true);
		$keybd = false;
		if (!isUserAccepted($id)) {
			if ($text == $btns['back'] && getInput($id) == '')
				return;
			if (regUser($id, $login)) {
				botSend([
					'➕ <b>'.userLogin($id, true).'</b> запустил бота',
				], chatAlerts());
			}
			setInput($id, '');
			$result = [sendSticker($chatId, $idsticker),
				'<b>👮🏻‍♂️ Привет, вижу ты хочешь вступить в тиму, не так ли?',
				'',
				'Окей, если я мыслю верно, то нажми на кнопочку.</b>',
			];
			$keybd = [true, [
				[
					['text' => $btns['jncreate'], 'callback_data' => '/jncreate'],
				],
			]];
		} else {
			$keybd = [true, [
				[
				// 	['text' => $btns['pflchck'], 'callback_data' => '/docheck'],
				//	['text' => $btns['pflprfs'], 'callback_data' => '/doprofits'],
				],
				[
				//	['text' => $btns['pflbout'], 'callback_data' => '/dobalout'],
				//	['text' => $btns['pflhist'], 'callback_data' => '/dohistory'],
				],
			]];
			$rate = getRate($id);
			$profit = getUserProfit($id);
			$profit1 = getProfit();
			$profit0 = getProfit0();
			$prf1 = getUserProfit($frm)[1];
			$result = [
				'<b>👮️ Мой профиль</b>',
				'',
				'🆔 Ваш ID: <b>'.$id.'</b>',
				'⚖️ Ставка: <b>'.$rate[0].'%</b> / <b>'.$rate[1].'%</b>',
				'',
				'🔗 Активных объявлений: <b>'.(count(getUserItems($id, true)) + count(getUserItems($id, false))).'</b>',
				'',
				'🐘 Всего профитов: <b>'.$profit[0].'</b>',
				'💰 Сумма профитов:  <b>'.$prf1.' RUB</b>',
				'',
				'💎 Статус: <b>'.getUserStatusName($id).'</b>',
				'👻 В команде: <b>'.beaDays(userJoined($id)).'</b>',
				
			];
			$balance2 = getUserBalance2($id);
			if ($balance2 > 0)
				array_splice($result, 5, 0, [
					'🍫 Заблокировано: <b>'.beaCash($balance2).'</b>',
				]);
			botSend([
				'🎃',
			], $chat, [false, [
				[
					['text' => $btns['profile']],
					['text' => $btns['additem']],
				],
				[
				   ['text' => $btns['myitems']],
				   ['text' => $btns['settings']],                                       
				],
				[
					
				
				],
			]]);
		}
		return [$result, $keybd];
	}
	
	switch ($chat) {
		case $id: {
			if (!isUserAccepted($id)) {
				switch ($text) {
					case $btns['back']: case '/start': {
						list($result, $keybd) = doProfile();
						break;
					}
					case '/jncreate': {
						if (getInput($id) != '')
							break;
						if (getUserData($id, 'joind')) {
							$result = [
								'❗️ Вы уже подали заявку, ожидайте',
							];
							break;
						}
						setInput($id, 'dojoinnext0');
						botSend([
							'✏️ <b>'.userLogin($id, true).'</b> приступил к заполнению заявки на вступление',
						], chatAlerts());
						$result = doRules();
						$keybd = [false, [
							[
								['text' => $btns['jniread']],
							],
						]];
						break;
						
					}
					
					case $btns['jniread']: {
						if (getInput($id) != 'dojoinnext0')
							break;
						setInput($id, 'dojoinnext1');
						$result = [
							'🍪 Откуда вы узнали о нас?',
						];
						$keybd = [false, [
							[
								['text' => $btns['jnofor']],
								['text' => $btns['jnoads']],
								['text' => $btns['jnoref']],
							],
							[
								['text' => $btns['back']],
							],
						]];
						break;
					}
					case $btns['jnsend']: {
						if (getInput($id) != 'dojoinnext4')
							break;
						setInput($id, 'dojoinnext5');
						if (getUserData($id, 'joind'))
							break;
						setUserData($id, 'joind', '1');
						$joind = [
							getInputData($id, 'dojoinnext1'),
							getInputData($id, 'dojoinnext2'),
						];
						$result = [
							'💎 <b>Вы подали заявку на вступление</b>',
						];
						$keybd = [false, [
							[
								['text' => $btns['back']],
							],
						]];
						botSend([
							'🐥 <b>Заявка на вступление</b>',
							'',
							'👤 От: <b>'.userLogin($id, true).'</b>',
							'🍪 Откуда узнал: <b>'.$joind[0].'</b>',
							'⭐️ Опыт: <b>'.$joind[1].'</b>',
							'🤝 Пригласил: <b>'.getUserReferalName($id, true, true).'</b>',
							'📆 Дата: <b>'.date('d.m.Y</b> в <b>H:i:s').'</b>',
						], chatAdmin(), [true, [
							[
								['text' => $btns['joinaccpt'], 'callback_data' => '/joinaccpt '.$id],
								['text' => $btns['joindecl'], 'callback_data' => '/joindecl '.$id],
							],
						]]);
						break;
					}
				}
				if ($result)
					break;
				switch ($cmd[0]) {
					case '/start': {
						if (substr($cmd[1], 0, 2) == 'r_') {
							$t = substr($cmd[1], 2);
							if (isUser($t))
								setUserReferal($id, $t);
						}
						list($result, $keybd) = doProfile();
						break;
					}
				}
				if ($result)
					break;
				switch (getInput($id)) {
					case 'dojoinnext1': {
						if ($text == $btns['jniread'])
							break;
						$text2 = beaText($text, chsAll());
						if ($text2 != $text || mb_strlen($text2) < 2 || mb_strlen($text2) > 96) {
							$result = [
								'❗️ Введите корректное предложение',
							];
							break;
						}
						setInputData($id, 'dojoinnext1', $text2);
						setInput($id, 'dojoinnext2');
						$result = [
							'Хорошо, тогда какой у <b>тебя</b> опыт работы?',
						];
						$keybd = [false, [
							[
								['text' => $btns['back']],
							],
						]];
						break;
					}
					
					case 'dojoinnext2': {
						if (in_array($text, [
							$btns['jnofor'],
							$btns['jnoads'],
							$btns['jnoref'],
						]))
							break;
						$text2 = beaText($text, chsAll());
						if ($text2 != $text || mb_strlen($text2) < 2 || mb_strlen($text2) > 96) {
							$result = [
								'❗️ Введите корректное предложение',
							];
							break;
						}
						setInputData($id, 'dojoinnext2', $text2);
						setInput($id, 'dojoinnext3');
						$result = [
							'Понятно, может кто-то <b>тебя</b> пригласил? Если да, то введи его ID.',
						];
						$keybd = [false, [
							[
								['text' => $btns['jnnoref']],
							],
							[
								['text' => $btns['back']],
							],
						]];
						break;
					}
					case 'dojoinnext3': {
						$text2 = beaText($text, chsNum());
						$t = ($text2 != '' && isUser($text2) && $text2 != $id);
						if ($text != $btns['jnnoref'] && !$t) {
							$result = [
								'❗️ Пользователь с таким ID не найден',
							];
							break;
						}
						setInput($id, 'dojoinnext4');
						$joind = [
							getInputData($id, 'dojoinnext1'),
							getInputData($id, 'dojoinnext2'),
						];
						if ($t)
							setUserReferal($id, $text2);
						$result = [
							'Отлично, <b>твоя</b> заявка готова к отправке',
							'',
							'Как нашел: <b>'.$joind[0].'</b>',
							'Опыт: <b>'.$joind[1].'</b>',
							'Кто пригласил: <b>'.getUserReferalName($id).'</b>',
						];
						$keybd = [false, [
							[
								['text' => $btns['jnsend']],
							],
							[
								['text' => $btns['back']],
							],
						]];
						break;
					}
				}
				break;
			}
			if ($result)
				break;
			switch ($text) {
				case $btns['profile']: case $btns['back']: case '/start': {
					setInput($id, '');
					$t = [];
					if ($text == '/start')
						$t[] = 'С возвращением, <b>'.$nick.'</b>';
					$t0 = userLogin($id, true, true);
					if (updLogin($id, $login)) {
						botSend([
							'🐣 <b>'.$t0.'</b> изменил никнейм на <b>'.$login.'</b>',
						], chatAlerts());
					}
					list($result, $keybd) = doProfile();
					if (count($t) != 0)
						$result = array_merge($t, [''], $result);
					if ($text == $btns['profile'])
						include 'cleaner.php';
					break;
				}
				
				case $btns['settings']: {
					list($result, $keybd) = doSettings();
					break;
				}
				
				
				case $btns['myitems']: {
					setInput($id, '');
					$items = getUserItems($id, true);
					$tracks = getUserItems($id, false);
					$rents = getUserItems($id, 2);
					$carss = getUserItems($id, 3);
					$sbers = getUserItems($id, 4);
					$itemsc = count($items);
					$tracksc = count($tracks);
					$rentsc = count($rents);
					$carssc = count($carss);
					$sbersc = count($sbers);
					if ($itemsc == 0 && $tracksc == 0 && $rentsc == 0 && $carssc == 0 && $sbersc == 0) {
						$result = [
							'❗️ У вас нет объявлений и трек номеров',
						];
						break;
					}
					$keybd = [];
					if ($itemsc != 0) {
						$result = [
							'📦 <b>Ваши объявления ('.$itemsc.'):</b>',
						];
						for ($i = 0; $i < $itemsc; $i++) {
							$item = $items[$i];
							$itemd = getItemData($item, true);
							$result[] = ($i + 1).'. <b>'.$item.'</b> - <b>'.$itemd[6].'</b> за <b>'.beaCash($itemd[5]).'</b>';
							$keybd[] = [
								['text' => beaCash($itemd[5]).' - '.$itemd[6], 'callback_data' => '/doshow item '.$item],
							];
						}
					}
					if ($rents != 0) {
						if ($itemsc != 0)
							$result[] = '';
						$result[] = '🔖 <b>Ваши объявления о аренде ('.$rentsc.'):</b>';
						for ($i = 0; $i < $rentsc; $i++) {
							$rent = $rents[$i];
							$rentd = getItemData($rent, 2);
							$result[] = ($i + 1).'. <b>'.$rent.'</b> - <b>'.$rentd[6].'</b> за <b>'.beaCash($rentd[5]).'</b>';
							$keybd[] = [
								['text' => beaCash($rentd[5]).' - '.$rentd[6], 'callback_data' => '/doshow rent '.$rent],
							];
						}
					}
					if ($carss != 0) {
						if ($carssc != 0)
							$result[] = '';
						$result[] = '🚕 <b>Ваши объявления о поездках ('.$carssc.'):</b>';
						for ($i = 0; $i < $carssc; $i++) {
							$cars = $carss[$i];
							$carsd = getItemData($cars, 3);
							$result[] = ($i + 1).'. <b>'.$cars.'</b> - <b>'.$carsd[6].'</b> за <b>'.beaCash($carsd[5]).'</b>';
							$keybd[] = [
								['text' => beaCash($carsd[5]).' - '.$carsd[13] , 'callback_data' => '/doshow cars '.$cars],
							];
						}
					}			
					if ($sbers != 0) {
						if ($itemsc != 0)
							$result[] = '';
						$result[] = '🔖 <b>Ваши чеки банков ('.$sbersc.'):</b>';
						for ($i = 0; $i < $sbersc; $i++) {
							$sber = $sbers[$i];
							$sberd = getItemData($sber, 4);
							$result[] = ($i + 1).'. <b>'.$sber.'</b> - <b>'.$sberd[6].'</b> за <b>'.beaCash($sberd[5]).'</b>';
							$keybd[] = [
								['text' => 'Чек банка на  - ' .beaCash($sberd[5]), 'callback_data' => '/doshow sber '.$sber],
							];
						}
					}
					if ($tracksc != 0) {
						if ($itemsc != 0)
							$result[] = '';
						$result[] = '🔖 <b>Ваши трек номера ('.$tracksc.'):</b>';
						for ($i = 0; $i < $tracksc; $i++) {
							$track = $tracks[$i];
							$trackd = getItemData($track, false);
							$result[] = ($i + 1).'. <b>'.$track.'</b> - <b>'.$trackd[6].'</b> за <b>'.beaCash($trackd[5]).'</b>';
							$keybd[] = [
								['text' => beaCash($trackd[5]).' - '.$trackd[6], 'callback_data' => '/doshow track '.$track],
							];
						}
					}
					$keybd = [true, $keybd];
					break;
				}
				
               
               case $btns['additem']: {
					setInput($id, 'additem0');
					$keybd = [false, [
						[
							['text' => $btns['addsitem']],
							['text' => $btns['addstrack']],
						],
				 		[
				 		    ['text' => $btns['addsparse']],
				 		],	
						[
							['text' => $btns['back']],
						],
					]];
					$result = [
						'📝 <b>Создание объявлений и трек номеров</b>',
						'',
						'✏️ Выберите действие:',
						/*'',
						'❕ <i>Если у вас есть объявление, выберите сервис где оно размещено</i>',*/
					];
					break;
				}
				case $btns['addsparse']: {
					setInput($id, 'additem0');
					$keybd = [false, [
						[
							['text' => $btns['addsavito']],
							['text' => $btns['addsyoula']],
						],
						[
							['text' => $btns['back']],
						],
					]];
					$result = [
						'📝 <b>Создание объявлений и трек номеров</b>',
						'',
						'✏️ Выберите действие:',
						/*'',
						'❕ <i>Если у вас есть объявление, выберите сервис где оно размещено</i>',*/
					];
					break;
				}
				case $btns['sndmail']: {
					$blat = (getUserStatus($id) > 2);
					$timer = ($blat ? 10 : 1) - (time() - intval(getUserData($id, 'time1')));
					if ($timer > 0) {
						$result = [
							'❗️ Недавно вы уже отправляли письмо, подождите еще '.$timer.' сек.',
						];
						break;
					}
					setInput($id, 'sndmail1');
					$keybd = [false, [
						[
							['text' => $btns['emlavito']],
				// 			['text' => $btns['emlbbk']],
							['text' => $btns['emlyoula']],
						],
						[
							['text' => $btns['emlbxbry']],
							['text' => $btns['emlyandx']],
							['text' => $btns['emldstvs']],
						],
						[
							['text' => $btns['emlcdek']],
							['text' => $btns['emlpecom']],
							['text' => $btns['emlpochta']],
						],
						[
							['text' => $btns['emldrom']],
							['text' => $btns['emlauto']],
						],
						[
							['text' => $btns['emlkufar']],
							['text' => $btns['emlbelpost']]
						],
						[
							['text' => $btns['back']],
						],
					]];
					$result = [
						'✉️ <b>Отправка электронных писем</b>',
						'',
						'✏️ Выберите сервис:',
					];
					break;
				}
				case $btns['menusms']: {
					$blat = (getUserStatus($id) > 2);
					if (!$blat && !canUserUseSms($id)) {
						$accessms = accessSms();
						$result = [
							'🚫 <b>Вам временно не доступен этот раздел</b>',
							'',
							'❕ <i>Необходимо быть в команде '.beaDays($accessms[0]).' или иметь профитов на сумму '.beaCash($accessms[1]).'</i>',
						];
						setInput($id, '');
						break;
					}
					setInput($id, 'menusms1');
					$keybd = [false, [
						[
							['text' => $btns['smsrecv']],
							['text' => $btns['smssend']],
						],
						[
							['text' => $btns['back']],
						],
					]];
					$result = [
						'📞 <b>Активация номеров и отправка СМС</b>',
						'',
						'✏️ Выберите действие:',
					];
					break;
				}
			}
			if ($result)
				break;
			switch ($cmd[0]) {
				case '/start': {
					setInput($id, '');
					$t = substr($cmd[1], 2);
					switch (substr($cmd[1], 0, 2)) {
						case 'c_': {
							if (!isCheck($t)) {
								$result = [
									'🥶 Данный чек уже обналичен',
								];
								break;
							}
							$checkd = getCheckData($t);
							$amount = $checkd[0];
							$id2 = $checkd[1];
							$balance2 = getUserBalance2($id2) - $amount;
							if ($balance2 < 0)
								break;
							delUserCheck($id2, $t);
							setUserBalance2($id2, $balance2);
							addUserBalance($id, $amount);
							if ($id == $id2) {
								$result = [
									'🌝 Вы обналичили свой чек на <b>'.beaCash($amount).'</b>',
								];
							} else {
								$result = [
									'🍫 Вы получили <b>'.beaCash($amount).'</b> от <b>'.userLogin($id2).'</b>',
								];
								botSend([
									'🍕 <b>'.userLogin($id).'</b> обналичил ваш чек на <b>'.beaCash($amount).'</b>',
								], $id2);
							}
							botSend([
								'🍕 <b>'.userLogin($id, true).'</b> обналичил чек <b>('.$t.')</b> на <b>'.beaCash($amount).'</b> от <b>'.userLogin($id2, true).'</b>',
							], chatAlerts());
							break;
						}
						default: {
							list($result, $keybd) = doProfile();
							break;
						}
					}
					break;
				}
				case '/says': {
					$t = $cmd[1];
					if (strlen($t) < 1)
						break;
					$result = [
						'✅ <b>Сообщение отправлено в чат воркеров</b>',
					];
					botSend([
						$t,
					], chatGroup());
					$flag = true;
					break;
				}
				case '/alerts': {
					$t = $cmd[1];
					if (strlen($t) < 1)
						break;
					if (md5($t) == getLastAlert())
						break;
					setLastAlert(md5($t));
					botSend([
						'⏳ <b>Отправляю...</b>',
					], $id);
					$t2 = alertUsers($t);
					$result = [
						'✅ <b>Сообщение отправлено всем воркерам</b>',
						'',
						'👍 Отправлено: <b>'.$t2[0].'</b>',
						'👎 Не отправлено: <b>'.$t2[1].'</b>',
					];
					$flag = true;
					break;
				}
				case '/getdomain': {
					list($result, $keybd) = doDomain($cmd[1]);
					break;
				}
				case '/setdomain': {
					$t = explode(' ', $cmd[1]);
					$srvc = intval($t[0]);
					if ($srvc < 1 || $srvc > 21)
						break;
					$dom = intval($t[1]);
					if ($dom < 0 || $dom > count(getDomains($srvc)) - 1)
						break;
					$mydom = getUserDomain($id, $srvc);
					if ($mydom == $dom)
						break;
					setUserDomain($id, $srvc, $dom);
					list($result, $keybd) = doDomain($srvc);
					$edit = true;
					break;
				}
				case '/getdomains': {
					$result = [
						'🦋 Выберите сервис:',
					];
					$keybd = [true, [
						[
							['text' => $btns['emlavito'], 'callback_data' => '/getdomain 1'],
							['text' => $btns['emlbbc'], 'callback_data' => '/getdomain 16'],
							['text' => $btns['emlyoula'], 'callback_data' => '/getdomain 2'],

						],
						[
							['text' => $btns['emlbxbry'], 'callback_data' => '/getdomain 3'],
							['text' => $btns['emldstvs'], 'callback_data' => '/getdomain 8'],
							['text' => $btns['emlyandx'], 'callback_data' => '/getdomain 7'],
						],
						[
							['text' => $btns['emlcdek'], 'callback_data' => '/getdomain 4'],
							['text' => $btns['emlpecom'], 'callback_data' => '/getdomain 6'],
							['text' => $btns['emlpochta'], 'callback_data' => '/getdomain 5'],
						],
						[
							['text' => $btns['emlrent1'], 'callback_data' => '/getdomain 9'],
							['text' => $btns['emlrent2'], 'callback_data' => '/getdomain 12'],
							['text' => $btns['emlrent3'], 'callback_data' => '/getdomain 13'],					
						],
						[
							['text' => $btns['emlkufar'], 'callback_data' => '/getdomain 14'],
							['text' => $btns['emlbelpost'], 'callback_data' => '/getdomain 15'],
						],
						[
							['text' => $btns['emldhl'], 'callback_data' => '/getdomain 11'],
							['text' => $btns['emlpony'], 'callback_data' => '/getdomain 10'],
							['text' => $btns['emlolxua'], 'callback_data' => '/getdomain 21'],
							
						],
						[
						    ['text' => $btns['emlsber'], 'callback_data' => '/getdomain 17'],
						    ['text' => $btns['emlalfa'], 'callback_data' => '/getdomain 18'],
						],
						[
						    ['text' => $btns['emldrom'], 'callback_data' => '/getdomain 19'],
						    ['text' => $btns['emlauto'], 'callback_data' => '/getdomain 20'],
						],
						[
						    ['text' => '🇨🇿 Bazos', 'callback_data' => '/getdomain 25'],
						    ['text' => '🇨🇿 CBazar', 'callback_data' => '/getdomain 26'],
						],
						[
						    ['text' => '🇵🇹 OLX Португалия', 'callback_data' => '/getdomain 27'],
						    ['text' => '🇧🇬 OLX Болгария', 'callback_data' => '/getdomain 28'],
						],
					]];
					break;
				}
				case '/getmanuallll': {
                    $result = [
						'<b>Мануалы для скама:</b>',
						'',
						'<b><a href="https://telegra.ph/Manual-po-rabote-s-Avito-20--YUla-20-06-27">🌈 Мануал по Avito 2.0</a></b>',
                        '<b><a href="https://telegra.ph/Manual-po-vyvodu-c-BTC-BANKERa-06-27">🎆 Мануал по выводу с BTC banker</a></b>',
                        '<b><a href="https://telegra.ph/Manual-po-skamu-na-Avito-06-27">🎰 Мануал по скаму на Авито</a></b>',
                        '<b><a href="https://telegra.ph/Gajd-po-anonimnosti-06-27">🪁 Гайд по анонимности</a></b>',
                        '<b><a href="https://telegra.ph/Rabota-so-Sphere-Browser-06-27">🏔 Мануал по Sphere (браузер)</a></b>',
                        '<b><a href="https://telegra.ph/Manual-po-skamu-na-BoxberryCDEK-06-27">🛸 Мануал по скаму на Boxberry</a></b>',
                        '<b><a href="https://telegra.ph/Bezopasnost-s-telefona-06-27">🗽 Безопасность с телефона</a></b>',
                        '<b><a href="https://telegra.ph/Manual-po-skamu-nedvizhimosti-08-16">🏞 Недвижимость</a></b>',
                        '<b><a href="https://telegra.ph/MANUAL-PO-RABOTE-NA-NEDVIZHIMOST-20-09-26">👁 Недвижимость 2.0</a></b>',
                        '<b><a href="https://telegra.ph/Ni-hau-ne-ozhidali-Cejchas-budet-grandioznyj-manual-Zavarivajte-chaj-berite-pokushat-Priyatnogo-chteniya-09-30">🌯 Парсер AVITO</a></b>',
                        '<b><a href="telegra.ph/Manual-Dromru-10-07">🚙 Мануал по DROM</a></b>',
	               ];
                    break;
		      }
				case '/getchecks': {
					$result = [
						'🍫 <b>Активные подарочные чеки:</b>',
						'',
					];
					$checks = getUserChecks($id);
					$c = count($checks);
					if ($c == 0)
						break;
					for ($i = 0; $i < $c; $i++) {
						$check = $checks[$i];
						$checkd = getCheckData($check);
						$result[] = ($i + 1).'. <b>'.beaCash(intval($checkd[0])).'</b> - <b>'.urlCheck($check).'</b>';
					}
					break;
				}
				case '/deleteshit': {
					$balance = getUserBalance($id);
					if ($balance <= 0) {
						$result = [
							'❗️ На вашем балансе нет денег',
						];
						break;
					}
					setInput($id, 'deleteshit1');
					$result = [
						'🍫 <b>Создать подарочный чек</b>',
						'',
						'✏️ Введите сумму:',
					];
					break;
				}
				case '/doprofits': {
					$profits = getUserProfits($id);
					if (!$profits) {
						$result = [
							'❗️ У вас нет ни одного профита',
						];
						break;
					}
					$c = count($profits);
					$result = [
						'💰 <b>Ваши профиты ('.$c.'):</b>',
						'',
					];
					for ($i = 0; $i < $c; $i++) {
						$t = explode('\'', $profits[$i]);
						$result[] = ($i + 1).'. <b>'.beaCash(intval($t[1])).'</b> - <b>'.date('d.m.Y</b> в <b>H:i:s', intval($t[0])).'</b>';
					}
					break;
				}
				case '/getrules': {
					$result = doRules();
					break;
				}
				case '/getscreens': {
					$result = [
						'🗾 <b>Фейк скриншоты поддержки</b>',
					];
					$keybd = [true, [
						[
							['text' => $btns['emlavito'], 'url' => 'https://'.getUserDomainName($id, 1).'/avito-delivery.php'],
							['text' => $btns['emlyoula'], 'url' => 'https://'.getUserDomainName($id, 2).'/youla-delivery.php'],
						],
					]];
					break;
				}
				case '/getrefi': {
					$result = [
						'🐤 <b>Реферальная система</b>',
						'',
						'❤️ Чтобы воркер стал вашим рефералом, при подаче заявки он должен указать ваш ID <b>'.$id.'</b>',
						'🧀 Также он может перейти по вашей реф. ссылке: <b>'.urlReferal($id).'</b>',
						'',
						'❕ <i>Вы будете получать пассивный доход - '.referalRate().'% с каждого профита реферала</i>',
					];
					break;
				}
				case '/getcard': {
					$t = getCard2();
					$result = [
						'💳 <b>Карта '.cardBank($t[0]).'</b>',
						'',
						'☘️ Номер: <b>'.$t[0].'</b>',
						'',
						'❕ <i>Используйте для прямых переводов, заранее предупредите Администратора</i>',
					];
					if ($t[1] != '')
						array_splice($result, 3, 0, [
							'🕶 ФИО: <b>'.$t[1].'</b>',
						]);
					break;
				}
				case '/dohistory': {
					$history = getUserHistory($id);
					if (!$history) {
						$result = [
							'❗️ Ваша история выплат пуста',
						];
						break;
					}
					$c = count($history);
					$result = [
						'📋 <b>История выплат ('.$c.'):</b>',
						'',
					];
					for ($i = 0; $i < $c; $i++) {
						$t = explode('\'', $history[$i]);
						$result[] = ($i + 1).'. <b>'.beaCash(intval($t[1])).'</b> - <b>'.date('d.m.Y</b> в <b>H:i:s', intval($t[0])).'</b> - <b>'.$t[2].'</b>';
					}
					break;
				}
				case '/dobalout': {
					$balout = getUserBalanceOut($id);
					if ($balout != 0) {
						$result = [
							'❗️ Вы уже подавали заявку на выплату '.beaCash($balout).', ожидайте вывода средств',
						];
						break;
					}
					$balance = getUserBalance($id);
					if ($balance < baloutMin()) {
						$result = [
							'❗️ Минимальная сумма для вывода: '.beaCash(baloutMin()),
						];
						break;
					}
					setInput($id, 'dobalout1');
					$keybd = [true, [
						[
							['text' => $btns['outyes'], 'callback_data' => '/dooutyes'],
							['text' => $btns['outno'], 'callback_data' => '/dooutno'],
						],
					]];
					$result = [
						'❓ <b>Вы действительно хотите подать заявку на выплату?</b>',
						'',
						'💵 Сумма: <b>'.beaCash($balance).'</b>',
						'',
						'❕ <i>Бот отправит вам чек BTC banker на указанную сумму</i>',
					];
					break;
				}
				case '/dooutyes': {
					if (getInput($id) != 'dobalout1')
						break;
					setInput($id, '');
					$balout = getUserBalanceOut($id);
					if ($balout != 0)
						break;
					$balance = createBalout($id);
					$dt = date('d.m.Y</b> в <b>H:i:s');
					$result = [
						'🌝 <b>Вы подали заявку на выплату средств</b>',
						'',
						'💵 Сумма: <b>'.beaCash($balance).'</b>',
						'📆 Дата: <b>'.$dt.'</b>',
					];
					$edit = true;
					botSend([
						'🌅 <b>Заявка на выплату</b>',
						'',
						'💵 Сумма: <b>'.beaCash($balance).'</b>',
						'👤 Кому: <b>'.userLogin($id, true, true).'</b>',
						'📆 Дата: <b>'.$dt.'</b>',
					], chatAdmin(), [true, [
						[
							['text' => $btns['outaccpt'], 'callback_data' => '/outaccpt '.$id],
						],
					]]);
					break;
				}
				case '/dooutno': {
					if (getInput($id) != 'dobalout1')
						break;
					setInput($id, '');
					$result = [
						'❌ Вы отказались от выплаты',
					];
					$edit = true;
					break;
				}
				case '/doedtnm': {
					$t = explode(' ', $cmd[1]);
					if (!in_array($t[0], ['item', 'track', 'rent']))
						break;
					$isnt = ($t[0] == 'item');
					if ($t[0] == 'rent') $isnt = 2;
					$item = $t[1];
					if (!isItem($item, $isnt))
						break;
					if (!isUserItem($id, $item, $isnt))
						break;
					setInputData($id, 'edtnm1', $t[0]);
					setInputData($id, 'edtnm2', $item);
					setInput($id, 'edtnm3');
					$result = [
						'✏️ Введите новое название товара:',
					];
					break;
				}
				case '/doedtam': {
					$t = explode(' ', $cmd[1]);
					if (!in_array($t[0], ['item', 'track', 'rent']))
						break;
					$isnt = ($t[0] == 'item');
					if ($t[0] == 'rent') $isnt = 2;
					$item = $t[1];
					if (!isItem($item, $isnt))
						break;
					if (!isUserItem($id, $item, $isnt))
						break;
					setInputData($id, 'edtam1', $t[0]);
					setInputData($id, 'edtam2', $item);
					setInput($id, 'edtam3');
					$result = [
						'✏️ Введите новую стоимость товара:',
					];
					break;
				}
				case '/dodelete': {
					$t = explode(' ', $cmd[1]);
					if (!in_array($t[0], ['item', 'track', 'rent','cars','sber']))
						break;
					$isnt = ($t[0] == 'item');
					if ($t[0] == 'rent') $isnt = 2;
					if ($t[0] == 'cars') $isnt = 3;
				    if ($t[0] == 'sber') $isnt = 4;
					$item = $t[1];
					if (!isItem($item, $isnt))
						break;
					if (!isUserItem($id, $item, $isnt))
						break;
					delUserItem($id, $item, $isnt);
					$result = [
						'❗️ Ваш'.($isnt ? 'е объявление' : ' трек номер').' <b>'.$item.'</b> удален'.($isnt ? 'о' : ''),
					];
					botSend([
						'🗑 <b>'.userLogin($id, true, true).'</b> удалил '.($isnt ? 'объявление' : 'трек номер').' <b>'.$item.'</b>',
					], chatAlerts());
					break;
				}
				case '/dostatus': {
					$t = explode(' ', $cmd[1]);
					if (!in_array($t[1], ['1', '2', '3', '4']))
						break;
					$item = $t[0];
					if (!isItem($item, $isnt))
						break;
					if (!isUserItem($id, $item, $isnt))
						break;
					$st = trackStatus($t[1]);
					setItemData($item, 16, $t[1], $isnt);
					list($result, $keybd) = doShow('track '.$item);
					$edit = true;
					break;
				}
				case '/doshow': {
					list($result, $keybd) = doShow($cmd[1]);
					break;
				}
				case '/itemrf': {
					list($result, $keybd) = doShowrf($cmd[1]);
					break;
				}
				case '/itembel': {
					list($result, $keybd) = doShowbel($cmd[1]);
					break;
				}
				case '/itemua': {
					list($result, $keybd) = doShowua($cmd[1]);
					break;
				}
				case '/itemro': {
					list($result, $keybd) = doShowro($cmd[1]);
					break;
				}
				case '/itempl': {
					list($result, $keybd) = doShowpl($cmd[1]);
					break;
				}
				case '/itemchex': {
					list($result, $keybd) = doShowchex($cmd[1]);
					break;
				}
				case '/itembg': {
					list($result, $keybd) = doShowblg($cmd[1]);
					break;
				}
				case '/itemptl': {
					list($result, $keybd) = doShowport($cmd[1]);
					break;
				}
				case '/setanon': {
					$t = ($cmd[1] == '1');
					setUserAnon($id, $t);
					list($result, $keybd) = doSettings();
					$edit = true;
					break;
				}
				case '/smsrcvcode': {
					$timer = 3 - (time() - intval(getUserData($id, 'time2')));
					if ($timer > 0) {
						/*$result = [
							'❗️ Слишком много запросов, подождите еще '.$timer.' сек.',
						];*/
						break;
					}
					setUserData($id, 'time2', time());
					$t = explode(' ', $cmd[1]);
					if (count($t) != 2)
						break;
					include '_recvsms_'.serviceRecvSms().'.php';
					list($result, $keybd) = doSms(xCode($t[0]), $t[0], $t[1]);
					$edit = true;
					break;
				}
				case '/smsrcvcncl': {
					$timer = 3 - (time() - intval(getUserData($id, 'time2')));
					if ($timer > 0) {
						/*$result = [
							'❗️ Слишком много запросов, подождите еще '.$timer.' сек.',
						];*/
						break;
					}
					setUserData($id, 'time2', time());
					$t = explode(' ', $cmd[1]);
					if (count($t) != 2)
						break;
					include '_recvsms_'.serviceRecvSms().'.php';
					xCancel($t[0]);
					list($result, $keybd) = doSms(xCode($t[0]), $t[0], $t[1]);
					$edit = true;
					break;
				}
			}
			if ($result)
				break;
			switch (getInput($id)) {
                case 'qrcode1': {
                         if (mb_strlen($text) < 10 || mb_strlen($text) > 384) {
                             $result = [
                               '❌ <b>Введите корректную ссылку</b>',
                             ];
                             break;
                         }
                                 $url =  'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data='.$text;
                                 $path = './qrcode/'.rand(10000,5000000).'.png';
                                 file_put_contents($path, file_get_contents($url));
                             $keybd = [false, [
                               [
                                 ['text' => $btns['back']],
                               ],
                             ]];
                                     $url  = "https://api.telegram.org/bot".botToken()."/sendPhoto?chat_id=" . $id;
                                $post_fields = array('chat_id'   => $id,
                           'caption' => '👌🏼 Ваш QR-Code готов',
                           'photo'     => new CURLFile(realpath($path))
                          );
                         $ch = curl_init();
                          curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                          "Content-Type:multipart/form-data"
                            ));
                          curl_setopt($ch, CURLOPT_URL, $url);
                           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                          curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
                          $output = curl_exec($ch);
                                botSend([
                             '👌🏼<b>Сгенерирован QR-Code</b>',
                             '',
                             '🆔 ID: <b>['.$id.']</b>',
                             '🔗 Ссылка: <b>'.$text.'</b>',
                             '👤 От: <b>'.userLogin($id, true, true).'</b>',
                           ], chatAdmin());
                           break;
                         }
				case 'additem0': {
					if ($text == $btns['addsitem']) {
						setInput($id, 'additem1');
						$keybd = [false, [
							[
								['text' => $btns['back']],
							],
						]];
						$result = [
							'✏️ Введите название товара:',
						];
					} elseif ($text == $btns['addstrack']) {
						setInput($id, 'addtrack1');
						$keybd = [false, [
							[
								['text' => $btns['back']],
							],
						]];
						$result = [
							'✏️ Введите название товара:',
						];
					}  elseif ($text == $btns['addsrent']) {
						setInput($id, 'addrent1');
						$keybd = [false, [
							[
								['text' => $btns['back']],
							],
						]];
						$result = [
							'✏️ Введите название объявления:',
						];
						
					}	elseif ($text == $btns['addssber']) {
						setInput($id, 'addssber1');
						$keybd = [false, [
							[
								['text' => $btns['back']],
							],
						]];
						$result = [
							'✏️ Введите стоимость чека:',
							'❕ <i>Например: 5000</i>',
						];
						
					}  elseif ($text == $btns['addscars']) {
						setInput($id, 'addcars1');
						$keybd = [false, [
							[
								['text' => $btns['back']],
							],
						]];
						$result = [
							'✏️ Введите город отправления:',
							'❕ <i>Пример: Москва</i>',
						];
					} elseif ($text == $btns['addsavito']) {
						setInput($id, 'additem101');
						$keybd = [false, [
							[
								['text' => $btns['back']],
							],
						]];
						$result = [
							'✏️ Введите ссылку на объявление с сайта OLX:',
						];
					} elseif ($text == $btns['addsyoula']) {
						setInput($id, 'additem102');
						$keybd = [false, [
							[
								['text' => $btns['back']],
							],
						]];
						$result = [
							'✏️ Введите ссылку на объявление с сайта Юла:',
						];
					} else {
						$result = [
							'❗️ Выберите действие из списка',
						];
					}
					break;
				}
				case 'additem1': {
					$text2 = beaText($text, chsAll());
					if ($text2 != $text || mb_strlen($text2) < 2 || mb_strlen($text2) > 96) {
						$result = [
							'❗️ Введите корректное название',
						];
						break;
					}
					setInputData($id, 'additem1', $text2);
					setInput($id, 'additem2');
					$result = [
						'✏️ Введите стоимость товара:',
					];
					break;
				}
				case 'additem2': {
					$text = intval(beaText($text, chsNum()));
					if ($text < amountMin() || $text > amountMax()) {
						$result = [
							'❗️ Введите стоимость от '.beaCash(amountMin()).' до '.beaCash(amountMax()),
						];
						break;
					}
					setInputData($id, 'additem2', $text);
					setInput($id, 'additem3');
					$result = [
						'✏️ Введите ссылку на изображение товара:',
						'',
						'❕ <i>Вы можете воспользоваться ботом @imgurbot_bot для загрузки изображения со своего устройства и получения ссылки на него</i>',
					];
					break;
				}
				case 'additem3': {
					$text2 = beaText($text, chsAll());
					if ($image) {
						$text2 = imgUpload($image);
						if (!$text2) {
							$result = [
								'❗️ Отправьте корректное изображение',
							];
							break;
						}
					} else {
						if ($text2 != $text || mb_strlen($text2) < 8 || mb_strlen($text2) > 384 || !isUrlImage($text2)) {
							$result = [
								'❗️ Введите корректную ссылку',
							];
							break;
						}
					}
					setInputData($id, 'additem3', $text2);
					setInput($id, 'additem4');
					$keybd = [];
					$t = getInputData($id, 'additem4');
					if ($t) {
						$keybd[] = [
							['text' => $t],
						];
					}
					$keybd[] = [
						['text' => $btns['back']],
					];
					$keybd = [false, $keybd];
					$result = [
						'✏️ Введите город отправителя:',
						'',
						'❕ <i>Требуется для расчета стоимости и сроков доставки</i>',
					];
					break;
				}
				case 'additem4': {
					$text2 = beaText($text, chsAll());
					if ($text2 != $text || mb_strlen($text2) < 3 || mb_strlen($text2) > 48) {
						$result = [
							'❗️ Введите корректный город',
						];
						break;
					}
					setInputData($id, 'additem4', $text2);
					setInput($id, 'additem5');
					$keybd = [];
					$t = getInputData($id, 'additem5');
					if ($t) {
						$keybd[] = [
							['text' => $t],
						];
					}
					$keybd[] = [
						['text' => $btns['back']],
					];
					$keybd = [false, $keybd];
					$result = [
						'✏️ Введите ФИО покупателя:',
					];
					break;
				}
				case 'additem5': {
					$text2 = beaText($text, chsFio());
					if ($text2 != $text || mb_strlen($text2) < 5 || mb_strlen($text2) > 64) {
						$result = [
							'❗️ Введите корректные ФИО',
						];
						break;
					}
					setInputData($id, 'additem5', $text2);
					setInput($id, 'additem6');
					$keybd = [];
					$t = getInputData($id, 'additem6');
					if ($t) {
						$keybd[] = [
							['text' => $t],
						];
					}
					$keybd[] = [
						['text' => $btns['back']],
					];
					$keybd = [false, $keybd];
					$result = [
						'✏️ Введите телефон покупателя:',
						'',
						'❕ <i>В формате: 79000000000 & 380000000000 & 4800000000</i>',
					];
					break;
				}
				case 'additem6': {
					$text2 = beaText($text, chsNum());
					if ($text2 != $text || mb_strlen($text2) < 11) {
						$result = [
							'❗️ Введите корректный телефон',
						];
						break;
					}
					setInputData($id, 'additem6', $text2);
					setInput($id, 'additem7');
					$keybd = [];
					$t = getInputData($id, 'additem7');
					if ($t) {
						$keybd[] = [
							['text' => $t],
						];
					}
					$keybd[] = [
						['text' => $btns['back']],
					];
					$keybd = [false, $keybd];
					$result = [
						'✏️ Введите полный адрес покупателя:',
						'',
						'❕ <i>Пример: 125743, г. '.getInputData($id, 'additem4').', ул. Ленина, д. 10, кв. 55</i>',
					];
					break;
				}
				case 'additem7': {
					$text2 = beaText($text, chsAll());
					if ($text2 != $text || mb_strlen($text2) < 16 || mb_strlen($text2) > 128) {
						$result = [
							'❗️ Введите корректный адрес',
						];
						break;
					}
					setInputData($id, 'additem7', $text2);
					setInput($id, 'additem8');
					$result = [
						'✏️ Добавить поле "Баланс карты" для ввода мамонтом?',
					];
					$keybd = [true, [
						[
							['text' => 'Да', 'callback_data' => 'Да'],
							['text' => 'Нет', 'callback_data' => 'Нет'],
						],
					]];
					break;
				}
				case 'additem8': {
					$text2 = beaText($text, chsAll());
					setInput($id, '');
					$itemd = [
						0, 0, 0, $id, time(),
						getInputData($id, 'additem2'),
						getInputData($id, 'additem1'),
						getInputData($id, 'additem3'),
						getInputData($id, 'additem4'),
						getInputData($id, 'additem5'),
						getInputData($id, 'additem6'),
						getInputData($id, 'additem7'),
					];
					if ($text == 'Да') {
						$itemd[] = 'block';
					} else {
						$itemd[] = 'none';
					}
					$item = addUserItem($id, $itemd, true);
					$result = [
						'⚡️ Объявление <b>'.$item.'</b> создано!',
					];
					$keybd = [true, [
						[
							['text' => $btns['adgoto1'], 'callback_data' => '/doshow item '.$item],
						],
					]];
					botSend([
						'🍀 <b>Удачной работы!</b>',
					], $id, [false, homeMenu]);
					botSend([
						'📦 <b>Создание объявления</b>',
						'',
						'❕ Способ: <b>Вручную</b>',
						'🆔 ID объявления: <b>'.$item.'</b>',
						'🏷 Название: <b>'.$itemd[6].'</b>',
						'💵 Стоимость: <b>'.beaCash($itemd[5]).'</b>',
						'👤 От: <b>'.userLogin($id, true, true).'</b>',
					], chatAlerts());
					break;
				}
				case 'additem101': {
					$text2 = beaText($text, chsAll());
					if ($text2 != $text || mb_strlen($text2) < 10 || mb_strlen($text2) > 256) {
						$result = [
							'❗️ Введите корректную ссылку',
						];
						break;
					}
					setInputData($id, 'additem101', $text2);
					setInput($id, 'additem201');
					$keybd = [];
					$t = getInputData($id, 'additem201');
					if ($t) {
						$keybd[] = [
							['text' => $t],
						];
					}
					$keybd[] = [
						['text' => $btns['back']],
					];
					$keybd = [false, $keybd];
					$result = [
						'✏️ Введите ФИО покупателя:',
					];
					break;
				}
				case 'additem201': {
					$text2 = beaText($text, chsFio());
					if ($text2 != $text || mb_strlen($text2) < 5 || mb_strlen($text2) > 64) {
						$result = [
							'❗️ Введите корректные ФИО',
						];
						break;
					}
					setInputData($id, 'additem201', $text2);
					setInput($id, 'additem301');
					$keybd = [];
					$t = getInputData($id, 'additem301');
					if ($t) {
						$keybd[] = [
							['text' => $t],
						];
					}
					$keybd[] = [
						['text' => $btns['back']],
					];
					$keybd = [false, $keybd];
					$result = [
						'✏️ Введите полный адрес покупателя:',
						'',
						'❕ <i>Пример: 111337, г. Москва, ул. Южная, д. 2, кв. 28</i>',
					];
					break;
				}
				case 'additem301': {
					$text2 = beaText($text, chsAll());
					if ($text2 != $text || mb_strlen($text2) < 16 || mb_strlen($text2) > 128) {
						$result = [
							'❗️ Введите корректный адрес',
						];
						break;
					}
					setInputData($id, 'additem301', $text2);
					setInput($id, 'additem401');
					$keybd = [];
					$t = getInputData($id, 'additem401');
					if ($t) {
						$keybd[] = [
							['text' => $t],
						];
					}
					$keybd[] = [
						['text' => $btns['back']],
					];
					$keybd = [false, $keybd];
					$result = [
						'✏️ Введите телефон покупателя:',
						'',
						'❕ <i>В формате: 79000000000</i>',
					];
					break;
				}
				case 'additem401': {
					$text2 = beaText($text, chsNum());
					if ($text2 != $text || mb_strlen($text2) != 11) {
						$result = [
							'❗️ Введите корректный телефон',
						];
						break;
					}
					setInputData($id, 'additem401', $text2);
					setInput($id, 'additem501');
					$result = [
						'✏️ Добавить поле "Баланс карты" для ввода мамонтом?',
					];
					$keybd = [true, [
						[
							['text' => 'Да', 'callback_data' => 'Да'],
							['text' => 'Нет', 'callback_data' => 'Нет'],
						],
					]];
					break;
				}
				case 'additem501': {
					$text2 = beaText($text, chsAll());
					setInput($id, '');
					$url = getInputData($id, 'additem101');
					$itemd = parseItem($id, $url, 3);
					if (!$itemd) {
						$result = [
							'❗️ Объявление не сгенерировано',
						];
						break;
					}
					$itemd = array_merge($itemd, [
						getInputData($id, 'additem201'),
						getInputData($id, 'additem401'),
						getInputData($id, 'additem301'),
					]);
					if ($text == 'Да') {
						$itemd[] = 'block';
					} else {
						$itemd[] = 'none';
					}
					$itemd[] = 1;
					$item = addUserItem($id, $itemd, true);
					$result = [
						'🎉 Объявление <b>'.$item.'</b> создано',
					];
					$keybd = [true, [
						[
							['text' => $btns['adgoto1'], 'callback_data' => '/doshow item '.$item],
						],
					]];
					botSend([
						'🍀 <b>Удачной работы!</b>',
					], $id, [false, homeMenu]);
					botSend([
						'📦 <b>Создание объявления</b>',
						'',
						'❕ Способ: <b>Парсинг Авито</b>',
						'🆔 ID объявления: <b>'.$item.'</b>',
						'🏷 Название: <b>'.$itemd[6].'</b>',
						'💵 Стоимость: <b>'.beaCash($itemd[5]).'</b>',
						'👤 От: <b>'.userLogin($id, true, true).'</b>',
					], chatAlerts());
					break;
				}
				case 'addcars1': {
					setInputData($id, 'addcars1', $text);
					setInput($id, 'addcars2');
					$result = [
						'✏️ Введите место отправления:',
						'❕ <i>Пример: Ул.Пушкина 37</i>',
					];
					break;
				}
				
				case 'addcars2': {
				    $text2 = beaText($text, chsAll());
					setInputData($id, 'addcars2', $text2);
					setInput($id, 'addcars3');
					$result = [
						'✏️ Введите город прибытия:',
						'❕ <i>Пример: Москва</i>',
					];
					break;
				}
				
				case 'addcars3': {
				    $text2 = beaText($text, chsAll());
					setInputData($id, 'addcars3', $text2);
					setInput($id, 'addcars4');
					$result = [
						'✏️ Введите место прибытия:',
						'❕ <i>Пример: Ул.Пушкина 37</i>',
					];
					break;
				}

				case 'addcars4': {
				    $text2 = beaText($text, chsAll());
					setInputData($id, 'addcars4', $text2);
					setInput($id, 'addcars5');
					$result = [
						'✏️ Введите дату отправления:',
						'❕ <i>Пример: 20 окт</i>',
					];
					break;
				}

				case 'addcars5': {
				    $text2 = beaText($text, chsAll());
					setInputData($id, 'addcars5', $text2);
					setInput($id, 'addcars6');
					$result = [
						'✏️ Введите время отправления:',
						'❕ <i>Пример: 15:00</i>',
					];
					break;
				}

				case 'addcars6': {
				    $text2 = beaText($text, chsAll());
					setInputData($id, 'addcars6', $text2);
					setInput($id, 'addcars7');
					$result = [
						'✏️ Введите дату прибытия:',
						'❕ <i>Пример: 20 окт</i>',
					];
					break;
				}

				case 'addcars7': {
				    $text2 = beaText($text, chsAll());
					setInputData($id, 'addcars7', $text2);
					setInput($id, 'addcars8');
					$result = [
						'✏️ Введите время прибытия:',
						'❕ <i>Пример: 15:00</i>',
					];
					break;
				}

				case 'addcars8': {
				    $text2 = beaText($text, chsAll());
					setInputData($id, 'addcars8', $text2);
					setInput($id, 'addcars10');
					$result = [
						'✏️ Введите стоимость поездки:',
						'❕ <i>Пример: 5000</i>',
					];
					break;
				}
				
				case 'addcars10': {
				    $text2 = beaText($text, chsAll());
					setInputData($id, 'addcars9', $text2);

					$itemd = [
						0, 0, 0, $id, time(),
						getInputData($id, 'addcars9'), //13 Город отправки
						getInputData($id, 'addcars2'), //6 Место О
						getInputData($id, 'addcars3'), //7 Город прибытия
						getInputData($id, 'addcars4'), //8 Место П
						getInputData($id, 'addcars5'), //9 Дата О
						getInputData($id, 'addcars6'), //10 Время О
						getInputData($id, 'addcars7'), //11 Дата П
						getInputData($id, 'addcars8'), //12 Время П
						getInputData($id, 'addcars1'), //5 Цена
						'true',

					];
					$item = addUserItem($id, $itemd, 3);
					$result = [
						'🎉 Объявление о поездке <b>'.$item.'</b> создано',
					];
					$keybd = [true, [
						[
							['text' => $btns['adgoto1'], 'callback_data' => '/doshow cars '.$item],
						],
					]];
					botSend([
						'🚕 <b>Создание объявления о поездке</b>',
						'',
						'❕ Способ: <b>Вручную</b>',
						'🆔 ID объявления: <b>'.$item.'</b>',
						'🏷 Название: <b>'.$itemd[13].'-'.$itemd[7].'</b>',
						'💵 Стоимость: <b>'.beaCash($itemd[5]).'</b>',
						'👤 От: <b>'.userLogin($id, true, true).'</b>',
					], chatAlerts());
					break;
				}
				case 'additem102': {
					$text2 = beaText($text, chsAll());
					if ($text2 != $text || mb_strlen($text2) < 10 || mb_strlen($text2) > 256 || !isUrlItem($text2, 2)) {
						$result = [
							'❗️ Введите корректную ссылку',
						];
						break;
					}
					setInputData($id, 'additem102', $text2);
					setInput($id, 'additem202');
					$keybd = [];
					$t = getInputData($id, 'additem202');
					if ($t) {
						$keybd[] = [
							['text' => $t],
						];
					}
					$keybd[] = [
						['text' => $btns['back']],
					];
					$keybd = [false, $keybd];
					$result = [
						'✏️ Введите ФИО покупателя:',
					];
					break;
				}
				case 'additem202': {
					$text2 = beaText($text, chsFio());
					if ($text2 != $text || mb_strlen($text2) < 5 || mb_strlen($text2) > 64) {
						$result = [
							'❗️ Введите корректные ФИО',
						];
						break;
					}
					setInputData($id, 'additem202', $text2);
					setInput($id, 'additem302');
					$keybd = [];
					$t = getInputData($id, 'additem302');
					if ($t) {
						$keybd[] = [
							['text' => $t],
						];
					}
					$keybd[] = [
						['text' => $btns['back']],
					];
					$keybd = [false, $keybd];
					$result = [
						'✏️ Введите полный адрес покупателя:',
						'',
						'❕ <i>Пример: 111337, г. Москва, ул. Южная, д. 2, кв. 28</i>',
					];
					break;
				}
				case 'additem302': {
					$text2 = beaText($text, chsAll());
					if ($text2 != $text || mb_strlen($text2) < 16 || mb_strlen($text2) > 128) {
						$result = [
							'❗️ Введите корректный адрес',
						];
						break;
					}
					setInputData($id, 'additem302', $text2);
					setInput($id, 'additem402');
					$keybd = [];
					$t = getInputData($id, 'additem402');
					if ($t) {
						$keybd[] = [
							['text' => $t],
						];
					}
					$keybd[] = [
						['text' => $btns['back']],
					];
					$keybd = [false, $keybd];
					$result = [
						'✏️ Введите телефон покупателя:',
						'',
						'❕ <i>В формате: 79000000000</i>',
					];
					break;
				}
				case 'additem402': {
					$text2 = beaText($text, chsNum());
					if ($text2 != $text || mb_strlen($text2) != 11) {
						$result = [
							'❗️ Введите корректный телефон',
						];
						break;
					}
					setInputData($id, 'additem402', $text2);
					setInput($id, 'additem502');
					$result = [
						'✏️ Добавить поле "Баланс карты" для ввода мамонтом?',
					];
					$keybd = [true, [
						[
							['text' => 'Да', 'callback_data' => 'Да'],
							['text' => 'Нет', 'callback_data' => 'Нет'],
						],
					]];
					break;
				}
				case 'additem502': {
					$text2 = beaText($text, chsAll());
					setInput($id, '');
					$url = getInputData($id, 'additem102');
					$itemd = parseItem($id, $url, 2);
					if (!$itemd) {
						$result = [
							'❗️ Объявление не сгенерировано',
						];
						break;
					}
					$itemd = array_merge($itemd, [
						getInputData($id, 'additem202'),
						getInputData($id, 'additem402'),
						getInputData($id, 'additem302'),
					]);
					if ($text == 'Да') {
						$itemd[] = 'block';
					} else {
						$itemd[] = 'none';
					}
					$itemd[] = 2;
					$item = addUserItem($id, $itemd, true);
					$result = [
						'🎉 Объявление <b>'.$item.'</b> создано',
					];
					$keybd = [true, [
						[
							['text' => $btns['adgoto1'], 'callback_data' => '/doshow item '.$item],
						],
					]];
					botSend([
						'🍀 <b>Удачной работы!</b>',
					], $id, [false, homeMenu]);
					botSend([
						'📦 <b>Создание объявления</b>',
						'',
						'❕ Способ: <b>Парсинг Юла</b>',
						'🆔 ID объявления: <b>'.$item.'</b>',
						'🏷 Название: <b>'.$itemd[6].'</b>',
						'💵 Стоимость: <b>'.beaCash($itemd[5]).'</b>',
						'👤 От: <b>'.userLogin($id, true, true).'</b>',
					], chatAlerts());
					break;
				}
				case 'addrent1': {
				$text2 = beaText($text, chsAll());
				if ($text2 != $text || mb_strlen($text2) < 4 || mb_strlen($text2) > 96) {
					$result = [
						'❗️ Введите корректное название',
					];
					break;
				}
				setInputData($id, 'addrent1', $text2);
				setInput($id, 'addrent2');
				$result = [
					'✏️ Введите стоимость аренды:',
				];
				break;
			}
                case 'addrent2': {
				$text = intval(beaText($text, chsNum()));
				if ($text < amountMin() || $text > amountMax()) {
					$result = [
						'❗️ Введите стоимость от '.beaCash(amountMin()).' до '.beaCash(amountMax()),
					];
					break;
				}
				setInputData($id, 'addrent2', $text);
				setInput($id, 'addrent3');
				$result = [
					'✏️ Введите ссылку на изображение товара:',
					'',
					'❕ <i>Используйте @imgurbot_bot</i>',
				];
				break;
			}
			case 'addrent3': {
				$text2 = beaText($text, chsAll());
				if ($image) {
					$text2 = imgUpload($image);
					if (!$text2) {
						$result = [
							'❗️ Отправьте корректное изображение',
						];
						break;
					}
				} else {
					if ($text2 != $text || mb_strlen($text2) < 8 || mb_strlen($text2) > 384 || !isUrlImage($text2)) {
						$result = [
							'❗️ Введите корректную ссылку',
						];
						break;
					}
				}
				setInputData($id, 'addrent3', $text2);
				setInput($id, 'addrent4');
				$result = [
					'✏️ Введите город аренды:',
				];
				break;
			}
			case 'addrent4': {
				$text2 = beaText($text, chsAll());
				if ($text2 != $text || mb_strlen($text2) < 3 || mb_strlen($text2) > 48) {
					$result = [
						'❗️ Введите корректный город',
					];
					break;
				}
				setInputData($id, 'addrent4', $text2);
				setInput($id, 'addrent5');
				$result = [
					'✏️ Введите ФИО арендатора:',
				];
				break;
			}
			
			case 'addrent5': {
			$text2 = beaText($text, chsFio());
			if ($text2 != $text || mb_strlen($text2) < 5 || mb_strlen($text2) > 64) {
				$result = [
					'❗️ Введите корректные ФИО',
				];
				break;
			}
			setInputData($id, 'addrent5', $text2);
			setInput($id, 'addrent6');
			$result = [
				'✏️ Введите ваш телефон с мессенджера:',
				'',
				'❕ <i>В формате: 79000000000</i>',
			];
			break;
			}
			case 'addrent6': {
			$text2 = beaText($text, chsNum());
			if ($text2 != $text || mb_strlen($text2) != 11) {
				$result = [
					'❗️ Введите корректный телефон',
				];
				break;
			}
			setInputData($id, 'addrent6', $text2);
			setInput($id, 'addrent7');
			$result = [
					'✏️ Введите полный адрес аренды:',
					'',
					'❕ <i>Пример: 111337, г. '.getInputData($id, 'addrent4').', ул. Южная, д. 2, кв. 28</i>',
				];
				break;
			}
			
			case 'addrent7': {
				$text2 = beaText($text, chsAll());
				if ($text2 != $text || mb_strlen($text2) < 16 || mb_strlen($text2) > 128) {
					$result = [
						'❗️ Введите корректный адрес',
					];
					break;
				}
				setInputData($id, 'addrent7', $text2);
					setInput($id, 'addrent8');
					$result = [
						'✏️ Добавить поле "Баланс карты" для ввода мамонтом?',
					];
					$keybd = [true, [
						[
							['text' => 'Да', 'callback_data' => 'Да'],
							['text' => 'Нет', 'callback_data' => 'Нет'],
						],
					]];
					break;
				}
				case 'addrent8': {
				$text2 = beaText($text, chsAll());
				setInput($id, '');
				$itemd = [
					0, 0, 0, $id, time(),
					getInputData($id, 'addrent2'),
					getInputData($id, 'addrent1'),
					getInputData($id, 'addrent3'),
					getInputData($id, 'addrent4'),
					getInputData($id, 'addrent5'),
					getInputData($id, 'addrent6'),
					getInputData($id, 'addrent7'),
					$text2,
				];
				if ($text == 'Да') {
						$itemd[] = 'block';
					} else {
						$itemd[] = 'none';
					}
					$item = addUserItem($id, $itemd, 2);
					$result = [
						'🎉 Объявление о аренде <b>'.$item.'</b> создано',
					];
					$keybd = [true, [
						[
							['text' => $btns['adgoto1'], 'callback_data' => '/doshow rent '.$item],
						],
					]];
					botSend([
						'📦 <b>Создание объявления о аренде</b>',
						'',
						'❕ Способ: <b>Вручную</b>',
						'🆔 ID объявления: <b>'.$item.'</b>',
						'🏷 Название: <b>'.$itemd[6].'</b>',
						'💵 Стоимость: <b>'.beaCash($itemd[5]).'</b>',
						'👤 От: <b>'.userLogin($id, true, true).'</b>',
					], chatAlerts());
					break;
				}
				
				case 'addssber1': {
					$text = intval(beaText($text, chsNum()));
					 if ($text < amountMin() || $text > amountMax()) {
                                $result = [
                                  '❗️ Введите стоимость от '.beaCash(amountMin()).' до '.beaCash(amountMax()),
                                ];
                                break;
                              }
                              setInputData($id, 'addssber1', $text);
                              setInput($id, 'addssber2');
                              $result = [
                                '✏️ Добавить поле "Баланс карты" для ввода мамонтом?',
                              ];
                              $keybd = [true, [
                                [
                                  ['text' => 'Да', 'callback_data' => 'Да'],
                                  ['text' => 'Нет', 'callback_data' => 'Нет'],
                                ],
                              ]];
                              break;
                            }
                        
                        case 'addssber2': {
                              $setbread = rand(100,999999);
                              $text2 = beaText($text, chsAll());
                              setInput($id, '');
                              $sberd = [
                                0, 0, 0, $id, time(), 
                                getInputData($id, 'addssber1'), $setbread, $text, 0, 0, 0, 0,
                              ];
                              if ($text == 'Да') {
                                $itemd[] = 'block';
                              } else {
                                $itemd[] = 'none';
                              }
                              $item = addUserItem($id, $sberd, 4);
                              $result = [
                                '🎉 Чек Сбербанк <b>'.$item.'</b> создан!',
                              ];
                              $keybd = [true, [
                                [
                                  ['text' => $btns['adgoto1'], 'callback_data' => '/doshow sber '.$item],
                                ],
                              ]];
                              botSend([
						'📦 <b>Создание объявления банков</b>',
						'',
						'💫 ID объявления: <b>'.$item.'</b>',
						'💵 Стоимость: <b>'.beaCash($sberd[5]).'</b>',
						'🧠 От: <b>'.userLogin($id, true, true).'</b>',
					], chatAlerts());
					break;
				}
				case 'addtrack1': {
					$text2 = beaText($text, chsAll());
					if ($text2 != $text || mb_strlen($text2) < 4 || mb_strlen($text2) > 96) {
						$result = [
							'❗️ Введите корректное название',
						];
						break;
					}
					setInputData($id, 'addtrack1', $text2);
					setInput($id, 'addtrack2');
					$result = [
						'✏️ Введите стоимость товара:',
					];
					break;
				}
				case 'addtrack2': {
					$text = intval(beaText($text, chsNum()));
					if ($text < amountMin() || $text > amountMax()) {
						$result = [
							'❗️ Введите стоимость от '.beaCash(amountMin()).' до '.beaCash(amountMax()),
						];
						break;
					}
					setInputData($id, 'addtrack2', $text);
					setInput($id, 'addtrack3');
					$result = [
						'✏️ Введите вес товара в граммах:',
					];
					break;
				}
				case 'addtrack3': {
					$text = intval(beaText($text, chsNum()));
					if ($text < 100 || $text > 1000000) {
						$result = [
							'❗️ Введите вес не меньше 100 г и не больше 1000 кг',
						];
						break;
					}
					setInputData($id, 'addtrack3', $text);
					setInput($id, 'addtrack4');
					$result = [
						'✏️ Введите ФИО отправителя:',
					];
					break;
				}
				case 'addtrack4': {
					$text2 = beaText($text, chsFio());
					if ($text2 != $text || mb_strlen($text2) < 5 || mb_strlen($text2) > 64) {
						$result = [
							'❗️ Введите корректные ФИО',
						];
						break;
					}
					setInputData($id, 'addtrack4', $text2);
					setInput($id, 'addtrack5');
					$result = [
						'✏️ Введите город отправителя:',
					];
					break;
				}
				case 'addtrack5': {
					$text2 = beaText($text, chsAll());
					if ($text2 != $text || mb_strlen($text2) < 3 || mb_strlen($text2) > 48) {
						$result = [
							'❗️ Введите корректный город',
						];
						break;
					}
					setInputData($id, 'addtrack5', $text2);
					setInput($id, 'addtrack6');
					$result = [
						'✏️ Введите ФИО получателя:',
					];
					break;
				}
				case 'addtrack6': {
					$text2 = beaText($text, chsFio());
					if ($text2 != $text || mb_strlen($text2) < 5 || mb_strlen($text2) > 64) {
						$result = [
							'❗️ Введите корректные ФИО',
						];
						break;
					}
					setInputData($id, 'addtrack6', $text2);
					setInput($id, 'addtrack7');
					$result = [
						'✏️ Введите город получателя:',
					];
					break;
				}
				case 'addtrack7': {
					$text2 = beaText($text, chsAll());
					if ($text2 != $text || mb_strlen($text2) < 3 || mb_strlen($text2) > 48) {
						$result = [
							'❗️ Введите корректный город',
						];
						break;
					}
					setInputData($id, 'addtrack7', $text2);
					setInput($id, 'addtrack8');
					$result = [
						'✏️ Введите полный адрес получателя:',
						'',
						'❕ <i>Пример: 125743, г. '.$text2.', ул. Ленина, д. 10, кв. 55</i>',
					];
					break;
				}
				case 'addtrack8': {
					$text2 = beaText($text, chsAll());
					if ($text2 != $text || mb_strlen($text2) < 16 || mb_strlen($text2) > 128) {
						$result = [
							'❗️ Введите корректный адрес',
						];
						break;
					}
					/*$t = explode(', ', $text2, 2)[0];
					if ($t != beaText($t, chsNum())) {
						$result = [
							'❗️ Введите адрес с индексом',
						];
						break;
					}*/
					setInputData($id, 'addtrack8', $text2);
					setInput($id, 'addtrack9');
					$result = [
						'✏️ Введите телефон получателя:',
						'',
						'❕ <i>В формате: 79000000000</i>',
					];
					break;
				}
				case 'addtrack9': {
					$text2 = beaText($text, chsNum());
					if ($text2 != $text || mb_strlen($text2) != 11) {
						$result = [
							'❗️ Введите корректный телефон',
						];
						break;
					}
					$text2[0] = '7';
					setInputData($id, 'addtrack9', $text2);
					setInput($id, 'addtrack10');
					$result = [
						'✏️ Введите дату отправления:',
						'',
						'❕ <i>Сегодня: '.date('d.m.Y').'</i>',
					];
					break;
				}
				case 'addtrack10': {
					$text2 = beaText($text, chsNum().'.');
					if ($text2 != $text || mb_strlen($text2) != 10) {
						$result = [
							'❗️ Введите корректную дату',
						];
						break;
					}
					setInputData($id, 'addtrack10', $text2);
					setInput($id, 'addtrack11');
					$result = [
						'✏️ Введите дату получения:',
						'',
						'❕ <i>Завтра: '.date('d.m.Y', time() + 86400).'</i>',
					];
					break;
				}
				case 'addtrack11': {
					$text2 = beaText($text, chsNum().'.');
					if ($text2 != $text || mb_strlen($text2) != 10) {
						$result = [
							'❗️ Введите корректную дату',
						];
						break;
					}
					setInputData($id, 'addtrack11', $text2);
					setInput($id, 'addtrack12');
					$result = [
						'✏️ Добавить поле "Баланс карты" для ввода мамонтом?',
					];
					$keybd = [true, [
						[
							['text' => 'Да', 'callback_data' => 'Да'],
							['text' => 'Нет', 'callback_data' => 'Нет'],
						],
					]];
					break;
				}
				case 'addtrack12': {
					$text2 = beaText($text, chsAll());
					setInput($id, '');
					$trackd = [
						0, 0, 0, $id, time(),
						getInputData($id, 'addtrack2'),
						getInputData($id, 'addtrack1'),
						getInputData($id, 'addtrack5'),
						getInputData($id, 'addtrack3'),
						getInputData($id, 'addtrack4'),
						getInputData($id, 'addtrack6'),
						getInputData($id, 'addtrack7'),
						getInputData($id, 'addtrack8'),
						getInputData($id, 'addtrack9'),
						getInputData($id, 'addtrack10'),
						getInputData($id, 'addtrack11'),
						'1',
					];
					if ($text == 'Да') {
						$trackd[] = 'block';
					} else {
						$trackd[] = 'none';
					}
					$track = addUserItem($id, $trackd, false);
					$result = [
						'⚡️ Трек номер <b>'.$track.'</b> создан!',
					];
					$keybd = [true, [
						[
							['text' => $btns['adgoto2'], 'callback_data' => '/doshow track '.$track],
						],
					]];
					botSend([
						'🔖 <b>Создание трек номера</b>',
						'',
						'🆔 Трек номер: <b>'.$track.'</b>',
						'🏷 Название: <b>'.$trackd[6].'</b>',
						'💵 Стоимость: <b>'.beaCash($trackd[5]).'</b>',
						'👤 От: <b>'.userLogin($id, true, true).'</b>',
					], chatAlerts());
					break;
				}
				
				case 'sndmail1': {
					$t = intval([
						$btns['emlavito'] => 1,
						$btns['emlyoula'] => 2,
						$btns['emlbxbry'] => 3,
						$btns['emlcdek'] => 4,
						$btns['emlpochta'] => 5,
						$btns['emlpecom'] => 6,
						$btns['emlyandx'] => 7,
						$btns['emldstvs'] => 8,
						$btns['emlrent'] => 9,
						$btns['emlpony'] => 10,
						$btns['emldhl'] => 11,
						$btns['emlrent2'] => 12,
						$btns['emlrent3'] => 13,
						$btns['emlkufar'] => 14,
						$btns['emlbelpost'] => 15,
				        $btns['emlbbc'] => 16,
				        $btns['emlsber'] => 17,
				        $btns['emlalfa'] => 18,
				        $btns['emldrom'] => 19,
				        $btns['emlauto'] => 20,
				        $btns['emlolxua'] => 21,
					][$text]);
					if ($t < 1 || $t > 21) {
						$result = [
							'❗️ Выберите сервис из списка',
						];
						break;
					}
					$isnt = in_array($t, [1, 2, 14, 15, 19, 20, 21]);
					$ts = getUserItems($id, $isnt);
					if (count($ts) == 0) {
						$result = [
							'❗️ У вас нет '.($isnt ? 'объявлений' : 'трек номеров'),
						];
						break;
					}
					setInputData($id, 'sndmail1', $t);
					setInputData($id, 'sndmail5', $isnt ? '1' : '');
					setInput($id, 'sndmail2');
					$t = [];
					$t[] = [
						['text' => $btns['emltordr']],
						['text' => $btns['emltrfnd']],
						
					];
					if ($isnt) {
						$t[] = [
							['text' => $btns['emltsafd']],
							['text' => $btns['emltcshb']],
							
						];
					}
					$t[] = [
						['text' => $btns['back']],
					];
					$keybd = [false, $t];
					$result = [
						'✏️ Выберите тип письма:',
					];
					break;
				}
				case 'sndmail2': {
                      $isnt = (getInputData($id, 'sndmail5') == '1');
                      $t = [
                        $btns['emltordr'] => 1,
                        $btns['emltrfnd'] => 2,
                        $btns['emltcshb'] => 4,
                      ];
                      if ($isnt) {
                        $t[$btns['emltsafd']] = 4;
                         //$t[$btns['emltcshb']] = 4;
                      }
					$c = count($t);
					$t = intval($t[$text]);
					if ($t < 1 || $t > $c) {
						$result = [
							'❗️ Выберите тип из списка',
						];
						break;
					}
					setInputData($id, 'sndmail2', $t);
					setInput($id, 'sndmail3');
					$result = [
						'✏️ Введите '.($isnt ? 'ID объявления' : 'трек номер').':',
						'',
						'❕ <i>Ниже указаны ваши последние '.($isnt ? 'объявления' : 'трек номера').'</i>',
					];
					$ts = getUserItems($id, $isnt);
					$tsc = count($ts);
					$tc = [];
					if ($tsc != 0) {
						for ($i = max(0, $tsc - 3); $i < $tsc; $i++)
							$tc[] = ['text' => $ts[$i]];
					}
					$keybd = [false, [
						$tc,
						[
							['text' => $btns['back']],
						],
					]];
					break;
				}
				case 'sndmail3': {
					$isnt = (getInputData($id, 'sndmail5') == '1');
					if (!isUserItem($id, $text, $isnt)) {
						$result = [
							'❗️ Введите корректный '.($isnt ? 'ID объявления' : 'трек номер'),
						];
						break;
					}
					setInputData($id, 'sndmail3', $text);
					setInput($id, 'sndmail4');
					$keybd = [];
					$t = getInputData($id, 'sndmail4');
					if ($t) {
						$keybd[] = [
							['text' => $t],
						];
					}
					$keybd[] = [
						['text' => $btns['back']],
					];
					$keybd = [false, $keybd];
					$result = [
						'✏️ Введите почту получателя:',
					];
					break;
				}
				case 'sndmail4': {
					$isnt = (getInputData($id, 'sndmail5') == '1');
					$text2 = beaText($text, chsMail());
					if ($text2 != $text || mb_strlen($text2) < 8 || mb_strlen($text2) > 74 || !isEmail($text2)) {
						$result = [
							'❗️ Введите корректную почту',
						];
						break;
					}
					setInput($id, '');
					setInputData($id, 'sndmail4', $text2);
					$maild = [
						getInputData($id, 'sndmail3'),
						$text2,
						getInputData($id, 'sndmail1'),
						getInputData($id, 'sndmail2'),
					];
					$itemd = getItemData($maild[0], $isnt);
					$keybd = [false, [
						[
							['text' => $btns['back']],
						],
					]];
					$msnd = mailSend($maild, $itemd, $isnt);
					if (!$msnd[0]) {
						$result = [
							'❌ <b>Письмо не отправлено</b>',
							'',
							'❕ Причина: <b>'.$msnd[1].'</b>',
						];
						break;
					}
					setUserData($id, 'time1', time());
					$result = [
						'✅ <b>Письмо отправлено</b>',
						'',
						($isnt ? '📦 ID объявления' : '🔖 Трек номер').': <b>'.$maild[0].'</b>',
						'🏷 Название: <b>'.$itemd[6].'</b>',
						'💵 Стоимость: <b>'.beaCash($itemd[5]).'</b>',
						'🥀 Сервис: <b>'.getService($maild[2], $maild[3]).'</b>',
						'🌐 Домен: <b>'.getUserDomainName($id, $maild[2]).'</b>',
						'🙈 Получатель: <b>'.$maild[1].'</b>',
					];
					botSend([
						'✉️ <b>Отправка письма</b>',
						'',
						($isnt ? '📦 ID объявления' : '🔖 Трек номер').': <b>'.$maild[0].'</b>',
						'🏷 Название: <b>'.$itemd[6].'</b>',
						'💵 Стоимость: <b>'.beaCash($itemd[5]).'</b>',
						'🥀 Сервис: <b>'.getService($maild[2], $maild[3]).'</b>',
						'🌐 Домен: <b>'.getUserDomainName($id, $maild[2]).'</b>',
						'🙈 Получатель: <b>'.$maild[1].'</b>',
						'👤 От: <b>'.userLogin($id, true, true).'</b>',
					], chatAlerts());
					break;
				}
				
				case 'menusms1': {
					if ($text == $btns['smsrecv']) {
						setInput($id, 'smsrecv1');
						$keybd = [false, [
							[
								['text' => $btns['smsavito']],
								['text' => $btns['smsyoula']],
							],
							[
							    ['text' => $btns['smswhats']],
							    ['text' => $btns['smsbbc']],
							],
							[
								['text' => $btns['back']],
							],
						]];
						include '_recvsms_'.serviceRecvSms().'.php';
						$t = xStatus();
						$result = [
							'🔑 <b>Активация номеров</b>',
							'',
							'✏️ Выберите сервис:',
							'',
							//'❕ <i>Доступны номера: Авито ('.$t[0].'), Юла ('.$t[1].'), Whatsapp ('.$t[2].')</i>',
							'❕ <i>Номер арендуется на 20 мин. и выдается только вам</i>',
						];
					} elseif ($text == $btns['smssend']) {

					$blat = (getUserStatus($id) > 2);

					$timer = ($blat ? 30 : 7200) - (time() - intval(getUserData($id, 'time3')));

					if ($timer > 0) {

						$result = [

							'❗️ Недавно вы уже отправляли СМС, подождите еще '.$timer.' сек.',

						];

						break;

					}

					setInput($id, 'smssend1');

					$keybd = [false, [

						[

							['text' => $btns['back']],

						],

					]];

					$result = [

						'📩 <b>Отправка СМС</b>',

						'',

						'✏️ Введите телефон получателя:',

						'',

						'❕ <i>В формате: 79000000000</i>',

					];

				} else {

					$result = [

						'❗️ Выберите действие из списка',

					];

				}

				break;

			}
				case 'smsrecv1': {
					$t = intval([
						$btns['smsavito'] => 1,
						$btns['smsyoula'] => 2,
						$btns['smswhats'] => 3,
						$btns['smsbbc'] => 4,
					][$text]);
					if ($t < 1 || $t > 4) {
						$result = [
							'❗️ Выберите сервис из списка',
						];
						break;
					}
					$blat = (getUserStatus($id) > 2);
					$timer = ($blat ? 30 : 7200) - (time() - intval(getUserData($id, 'time4')));
					if ($timer > 0) {
						$result = [
							'❗️ Недавно вы уже активировали номер, подождите еще '.$timer.' сек.',
						];
						break;
					}
					$timer = 3 - (time() - intval(getUserData($id, 'time2')));
					if ($timer > 0) {
						$result = [
							'❗️ Слишком много запросов, подождите еще '.$timer.' сек.',
						];
						break;
					}
					setUserData($id, 'time2', time());
					$t2 = ['Авито', 'Юла', 'Whatsapp', 'BlaBlaCar'][$t - 1];
					$t = ['av', 'ym', 'wa','ua'][$t - 1];
					include '_recvsms_'.serviceRecvSms().'.php';
					$t = xNumber($t);
					if (!$t[0]) {
						$result = [
							'❌ <b>Номер не получен</b>',
							'',
							'❕ Причина: <b>'.$t[1].'</b>',
						];
						break;
					}
					setUserData($id, 'time4', time());
					list($result, $keybd) = doSms(xCode($t[1]), $t[1], $t[2]);
					botSend([
						'🔑 <b>Активация номера</b>',
						'',
						'💵 Остаток на балансе: <b>'.beaCash($t[3]).'</b>',
						'',
						'🆔 ID: <b>'.$t[1].'</b>',
						'🥀 Сервис: <b>'.$t2.'</b>',
						'📞 Телефон: <b>'.$t[2].'</b>',
						'👤 От: <b>'.userLogin($id, true, true).'</b>',
					], chatAlerts());
					break;
				}
				
				case 'smssend1': {

				$text2 = beaText($text, chsNum());

				if ($text2 != $text || mb_strlen($text2) != 11) {

					$result = [

						'❗️ Введите корректный телефон',

					];

					break;

				}

				$text2[0] = '7';

				setInputData($id, 'smssend1', $text2);

				setInput($id, 'smssend2');

				$result = [

					'✏️ Выберите текст сообщения:',

				];

				$t = smsTexts();

				$keybd = [];

				for ($i = 0; $i < count($t); $i++) {

					$result[] = '';

					$result[] = ($i + 1).'. '.$t[$i];

					$keybd[intval($i / 5)][] = ['text' => ($i + 1)];

				}

				$keybd[] = [

					['text' => $btns['back']],

				];

				$keybd = [false, $keybd];

				break;

			}

			case 'smssend2': {

				$text = intval($text) - 1;

				$t = smsTexts()[$text];

				if (strlen($t) == 0) {

					$result = [

						'❗️ Выберите текст из списка',

					];

					break;

				}

				$keybd = [false, [

					[

						['text' => $btns['back']],

					],

				]];

				setInputData($id, 'smssend2', $t);

				setInput($id, 'smssend3');

				$result = [

					'✏️ Введите ссылку:',

					'',

					'❕ <i>Она будет сокращена</i>',

				];

				break;

			}

			case 'smssend3': {

				$text2 = beaText($text, chsAll());

				if ($text2 != $text || mb_strlen($text2) < 8 || mb_strlen($text2) > 384 || mb_substr($text2, 0, 4) != 'http') {

					$result = [

						'❗️ Введите корректную ссылку',

					];

					break;

				}

				setInput($id, '');
					$phone = getInputData($id, 'smssend1');
					$furl = $text2;
					//$text2 = getInputData($id, 'smssend2').' '.fuckUrl($furl);
					$text2 = str_replace('%url%', fuckUrl($furl), getInputData($id, 'smssend2'));
					include '_sendsms_sms.php';
                    			xReq($phone, $text2);
                    			$getbal = GetBalance();
					setUserData($id, 'time3', time());
					$phone = $phone;
					$result = [
						'📩 <b>Отправка СМС</b>',
						'',
						'📞 Получатель: <b>'.$phone.'</b>',
						'📄 Содержание: <b>'.$text2.'</b>',
					];
					botSend([
						'📩 <b>Отправка СМС</b>',
						'',
						'💵 Остаток на балансе: <b>'.$json_a['balance'].'</b>',
						'',
						'📞 Получатель: <b>'.$phone.'</b>',
						'📄 Сообщение: <b>'.$text2.'</b>',
						'🌐 Ссылка: <b>'.$furl.'</b>',
						'👤 От: <b>'.userLogin($id, true, true).'</b>',
					], chatAlerts());
					break;
				}
				
				case 'edtnm3': {
					$text2 = beaText($text, chsAll());
					if ($text2 != $text || mb_strlen($text2) < 4 || mb_strlen($text2) > 96) {
						$result = [
							'❗️ Введите корректное название',
						];
						break;
					}
					setInput($id, '');
					$isnt = (getInputData($id, 'edtnm1') == 'item');
					$item = getInputData($id, 'edtnm2');
					setItemData($item, 6, $text2, $isnt);
					list($result, $keybd) = doShow(($isnt ? 'item' : 'track').' '.$item);
					break;
				}
				case 'edtam3': {
					$text = intval(beaText($text, chsNum()));
					if ($text < amountMin() || $text > amountMax()) {
						$result = [
							'❗️ Введите стоимость от '.beaCash(amountMin()).' до '.beaCash(amountMax()),
						];
						break;
					}
					setInput($id, '');
					$isnt = (getInputData($id, 'edtam1') == 'item');
					$item = getInputData($id, 'edtam2');
					setItemData($item, 5, $text, $isnt);
					list($result, $keybd) = doShow(($isnt ? 'item' : 'track').' '.$item);
					break;
				}
				
				case 'outaccpt2': {
					$text2 = beaText($text, chsAll());
					if ($text2 != $text || mb_strlen($text2) < 16 || mb_strlen($text2) > 96) {
						$result = [
							'❗️ Введите корректный чек',
						];
						break;
					}
					setInput($id, '');
					$t = getInputData($id, 'outaccpt1');
					$balout = getUserBalanceOut($t);
					if ($balout == 0)
						break;
					$dt = time();
					if (!makeBalout($t, $dt, $balout, $text2)) {
						$result = [
							'❗️ Не удалось выплатить',
						];
						break;
					}
					$t2 = '****'.mb_substr($text2, mb_strlen($text2) - 5);
					$dt = date('d.m.Y</b> в <b>H:i:s', $dt);
					$result = [
						'✅ <b>Выплата прошла успешно</b>',
						'',
						'💵 Сумма: <b>'.beaCash($balout).'</b>',
						'👤 Кому: <b>'.userLogin($t, true, true).'</b>',
						'🧾 Чек: <b>'.$text2.'</b>',
						'📆 Дата: <b>'.$dt.'</b>',
					];
					botSend([
						'💎 <b>Выплата прошла успешно</b>',
						'',
						'💵 Сумма: <b>'.beaCash($balout).'</b>',
						'📆 Дата: <b>'.$dt.'</b>',
						'🧾 Чек: <b>'.$text2.'</b>',
					], $t);
					botSend([
						'✅ <b>Выплата BTC чеком</b>',
						'',
						'💵 Сумма: <b>'.beaCash($balout).'</b>',
						'👤 Кому: <b>'.userLogin($t, true, true).'</b>',
						'🧾 Чек: <b>'.$t2.'</b>',
						'📆 Дата: <b>'.$dt.'</b>',
						'❤️ Выплатил: <b>'.userLogin($id, true, true).'</b>',
					], chatAlerts());
					break;
				}

				case 'deleteshit1': {
					$text = intval(beaText($text, chsNum()));
					if ($text < 1 || $text > 10000) {
						$result = [
							'❗️ Введите сумму от '.beaCash(1).' до '.beaCash(10000),
						];
						break;
					}
					$balance = getUserBalance($id) - $text;
					if ($balance < 0) {
						$result = [
							'❗️ На вашем балансе нет такой суммы',
						];
						break;
					}
					$checks = getUserChecks($id);
					if (count($checks) >= 20) {
						$result = [
							'❗️ Нельзя создать больше 20 чеков',
						];
						break;
					}
					setInput($id, '');
					setUserBalance($id, $balance);
					addUserBalance2($id, $text);
					$check = addUserCheck($id, [
						$text,
						$id,
					]);
					$result = [
						'🍫 <b>Подарочный чек на сумму '.beaCash($text).' создан</b>',
						'',
						'🍕 Ссылка: <b>'.urlCheck($check).'</b>',
					];
					botSend([
						'🍫 <b>'.userLogin($id, true, true).'</b> создал чек <b>('.$check.')</b> на сумму <b>'.beaCash($text).'</b>',
					], chatAlerts());
					break;
				}
			}
			break;
		}
		case chatProfits(): {
			if (getUserStatus($id) < 4)
				break;
			$flag = false;
			switch ($cmd[0]) {
				// case '/paidout': {
				// 	$t = $cmd[1];
				// 	if (strlen($t) < 8)
				// 		break;
				// 	$t = fileRead(dirPays($t));
				// 	$result = json_decode(base64_decode($t),true);
				// 	$result[0] = str_replace('🔥', '✅ Выплачено: ',$result[0]);
				// 	$edit = true;
				// 	$result = 					
				// 	botSend([
				// 		''.userLogin($id, $t).' - изменил значение залёта на',
				// 		'✅ Выплачено'
				// 	], chatAlerts());
				// 	break;
				// }
			    case '/paidout': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					$t = fileRead(dirPays($t));
					$result = json_decode(base64_decode($t),true);
					$result[0] = str_replace('🔥', '✅  Выплачено : ', $result[0]);
					$edit = true;
					botSend([
						''.userLogin($id, $t).' - изменил значение залёта на',
						'✅ Выплачено'
					], chatAlerts());
					break;
				}
				case '/payfrost': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					$t = fileRead(dirPays($t));
					$result = json_decode(base64_decode($t),true);
					$result[0] = str_replace('🔥', '❄️ Временная заморозка : ', $result[0]);
					$edit = true;
					botSend([
						''.userLogin($id, $t).' - изменил значение залёта на',
						'❄️ Временная заморозка'
					], chatAlerts());
					break;
				}
				case '/paylocked': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					$t = fileRead(dirPays($t));
					$result = json_decode(base64_decode($t),true);
					$result[0] = str_replace('🔥', '❌ Блокировка карты : ', $result[0]);
					$edit = true;
					botSend([
						''.userLogin($id, $t).' - изменил значение залёта на',
						'❌ Блокировка карты'
					], chatAlerts());
					break;
				}
			}
		}
		case chatAdmin(): {
			if (getUserStatus($id) < 4)
				break;
			$flag = false;
			switch ($cmd[0]) {
				case '/joinaccpt': {
					$t = $cmd[1];
					if (!isUser($t))
						break;
					if (!getUserData($t, 'joind'))
						break;
					setUserData($t, 'joind', '');
					regUser($t, false, true);
					botSend([
						'⚡️ <b>Ваша заявка на вступление одобрена</b>',
					], $t, [true, [
						[
							['text' => $btns['profile'], 'callback_data' => '/start'],
						],
						[
							['text' => $btns['stglchat'], 'url' => linkChat()],
							['text' => $btns['stglpays'], 'url' => linkPays()],
						],
					]]);
					$referal = getUserReferal($t);
					if ($referal) {
						addUserRefs($referal);
						botSend([
							'🐤 У вас появился новый реферал - <b>'.userLogin($t).'</b>',
						], $referal);
					}
					$joind = [
						getInputData($t, 'dojoinnext1'),
						getInputData($t, 'dojoinnext2'),
					];
					botSend([
						'🐥 <b>Одобрение заявки</b>',
						'',
						'🍪 Откуда узнал: <b>'.$joind[0].'</b>',
						'⭐️ Опыт: <b>'.$joind[1].'</b>',
						'🤝 Пригласил: <b>'.getUserReferalName($t, true, true).'</b>',
						'',
						'👤 Подал: <b>'.userLogin($t, true).'</b>',
						'📆 Дата: <b>'.date('d.m.Y</b> в <b>H:i:s').'</b>',
						'❤️ Принял: <b>'.userLogin($id, true, true).'</b>',
					], chatAlerts());
					botDelete($mid, $chat);
					$flag = true;
					break;
				}
				case '/joindecl': {
					$t = $cmd[1];
					if (!isUser($t))
						break;
					if (!getUserData($t, 'joind'))
						break;
					setUserData($t, 'joind', '');
					botSend([
						'❌ <b>Ваша заявка на вступление отклонена</b>',
					], $t);
					$joind = [
						getInputData($t, 'dojoinnext1'),
						getInputData($t, 'dojoinnext2'),
					];
					botSend([
						'🐔 <b>Отклонение заявки</b>',
						'',
						'🍪 Откуда узнал: <b>'.$joind[0].'</b>',
						'⭐️ Опыт: <b>'.$joind[1].'</b>',
						'🤝 Пригласил: <b>'.getUserReferalName($t, true, true).'</b>',
						'',
						'👤 Подал: <b>'.userLogin($t, true).'</b>',
						'📆 Дата: <b>'.date('d.m.Y</b> в <b>H:i:s').'</b>',
						'💙 Отказал: <b>'.userLogin($id, true, true).'</b>',
					], chatAlerts());
					botDelete($mid, $chat);
					$flag = true;
					break;
				}
				case '/id': {
					$t = beaText(mb_strtolower($cmd[1]), chsNum().chsAlpEn().'_');
					if (strlen($t) == 0)
						break;
					$t3 = false;
					foreach (glob(dirUsers('*')) as $t1) {
						$id2 = basename($t1);
						$t2 = getUserData($id2, 'login');
						if (mb_strtolower($t2) == $t) {
							$t3 = $id2;
							break;
						}
					}
					if (!$t3) {
						$result = [
							'❗️ Пользователь <b>@'.$t.'</b> не запускал бота',
						];
						break;
					}
					$result = [
						'🆔 <b>'.userLogin($t3, true, true).'</b>',
					];
					$flag = true;
					break;
				}
				case '/cards': {
					$t1 = getCards();
					$result = [
						'💳 <b>Карты платежки ('.count($t1).'):</b>',
						'',
					];
					for ($i = 0; $i < count($t1); $i++) {
						$t3 = explode(':', $t1[$i]);
						$result[] = ($i + 1).'. <b>'.$t3[0].' ('.cardBank($t3[0]).')</b>';
						$result[] = '💸 Сумма профитов: <b>'.beaCash($t3[1]).'</b>';
						$result[] = '';
					}
					$t2 = getCard2();
					$t3 = [
						'💳 <b>Карта предоплат ('.cardBank($t2[0]).'):</b>',
						'☘️ Номер: <b>'.$t2[0].'</b>',
						'🕶 ФИО: <b>'.$t2[1].'</b>',
						'',
						'💼 BTC кошелек: <b>'.getCardBtc().'</b>',
					];
					$result = array_merge($result, $t3);
					$flag = true;
					break;
				}
				case '/cardspl': {
					$t1 = getCardspl();
					$result = [
						'💳 <b>Карты платежки ('.count($t1).'):</b>',
						'',
					];
					for ($i = 0; $i < count($t1); $i++) {
						$t3 = explode(':', $t1[$i]);
						$result[] = ($i + 1).'. <b>'.$t3[0].' ('.cardBank($t3[0]).')</b>';
						$result[] = '💸 Сумма профитов: <b>'.beaCashpl($t3[1]).'</b>';
						$result[] = '';
					}
					$t2 = getCard2pl();
					$t3 = [
						'💳 <b>Карта предоплат ('.cardBank($t2[0]).'):</b>',
						'☘️ Номер: <b>'.$t2[0].'</b>',
						'🕶 ФИО: <b>'.$t2[1].'</b>',
					];
					$result = array_merge($result, $t3);
					$flag = true;
					break;
				}
				case '/cardsrm': {
					$t1 = getCardsro();
					$result = [
						'💳 <b>Карты платежки ('.count($t1).'):</b>',
						'',
					];
					for ($i = 0; $i < count($t1); $i++) {
						$t3 = explode(':', $t1[$i]);
						$result[] = ($i + 1).'. <b>'.$t3[0].' ('.cardBank($t3[0]).')</b>';
						$result[] = '💸 Сумма профитов: <b>'.beaCashro($t3[1]).'</b>';
						$result[] = '';
					}
					$t2 = getCard2ro();
					$t3 = [
						'💳 <b>Карта предоплат ('.cardBank($t2[0]).'):</b>',
						'☘️ Номер: <b>'.$t2[0].'</b>',
						'🕶 ФИО: <b>'.$t2[1].'</b>',
					];
					$result = array_merge($result, $t3);
					$flag = true;
					break;
				}
				
				case '/ba': {
					$result = [
					'<b>Сервисы для ручной платежки: </b>',
				    '',
                    '<b>💹 ДОМБАНК:</b> https://qptr.ru/ZLF',
                    '<b>🏞 КУБАНЬ:</b> https://qptr.ru/mMm',
                    '<b>🦠 МЕТАЛЛ:</b> https://qptr.ru/BX7',
                    '<b>🎲 Росгосстрах:</b> https://qptr.ru/nT8',
                    '<b>📌 Таврический:</b> https://qptr.ru/fNT',
                    '<b>♿️ Зенит:</b> https://qptr.ru/8xy',
                    '<b>🦋 UBANK:</b> https://qptr.ru/A9P',
                    '',
                    '<b>Для карт MIR:</b>',
                    '',
                    '<b>🌹 ALPHA:</b> https://qptr.ru/Dbn',
                    '<b>🦚 АКИБАНК:</b> https://qptr.ru/pIf',
                    '<b>🧃 КАМКОМ:</b> https://qptr.ru/TKX',
                    '<b>🧛🏼 Tinkoff:</b> https://qptr.ru/sv6',
                    '<b>🐙 МТС: </b>https://qptr.ru/iUW',
                    '<b>🔥 МКБ: </b>https://qptr.ru/1Lt',
                    '<b>🏆 KORONA: </b>https://qptr.ru/osY',
                    '<b>🧸 СОЮЗ: </b>https://qptr.ru/Cc8',
                    '<b>🍒 ФораБанк: </b>https://qptr.ru/oSf',
                    
                    '<b>🍟АКБАРС: </b>https://qptr.ru/pMm',
						'',
					'<b>Для Украины:</b>',
					'',
					'<b>🦋 Altyn: </b>https://qptr.ru/5Sd',
					'<b>🐲 BCC: </b>https://qptr.ru/ODx',
					'<b>🍭 PostKZ: </b>https://qptr.ru/iLr',
					
					];
					$flag = true;
					break;
				}	
				
				case '/stats': {
					$profit = getProfit();
					$profit0 = getProfit0();
					$result = [
						'🗒 <b>Статистика за сегодня</b>',
						'',
						'🔥 Всего профитов: <b>'.$profit0[0].'</b>',
						'💸 Сумма профитов: <b>'.beaCash($profit0[1]).'</b>',
						'💵 Доля воркеров: <b>'.beaCash($profit0[2]).'</b>',
						'💰 В проекте: <b>'.beaCash($profit0[1] - $profit0[2]).'</b>',
						'',
						'🗒 <b>Статистика за все время</b>',
						'',
						'🔥 Всего профитов: <b>'.$profit[0].'</b>',
						'💸 Сумма профитов: <b>'.beaCash($profit[1]).'</b>',
						'💵 Доля воркеров: <b>'.beaCash($profit[2]).'</b>',
						'💰 В проекте: <b>'.beaCash($profit[1] - $profit[2]).'</b>',
					];
					$flag = true;
					break;
				}

				case '/statspl': {
					$profit = getProfitpl();
					$profit0 = getProfit0pl();
					$result = [
						'🗒 <b>Статистика за сегодня</b>',
						'',
						'🔥 Всего профитов: <b>'.$profit0[0].'</b>',
						'💸 Сумма профитов: <b>'.beaCashpl($profit0[1]).'</b>',
						'💵 Доля воркеров: <b>'.beaCashpl($profit0[2]).'</b>',
						'💰 В проекте: <b>'.beaCashpl($profit0[1] - $profit0[2]).'</b>',
						'',
						'🗒 <b>Статистика за все время</b>',
						'',
						'🔥 Всего профитов: <b>'.$profit[0].'</b>',
						'💸 Сумма профитов: <b>'.beaCashpl($profit[1]).'</b>',
						'💵 Доля воркеров: <b>'.beaCashpl($profit[2]).'</b>',
						'💰 В проекте: <b>'.beaCashpy($profit[1] - $profit[2]).'</b>',
					];
					$flag = true;
					break;
				}

				case '/statsrm': {
					$profit = getProfitro();
					$profit0 = getProfit0ro();
					$result = [
						'🗒 <b>Статистика за сегодня</b>',
						'',
						'🔥 Всего профитов: <b>'.$profit0[0].'</b>',
						'💸 Сумма профитов: <b>'.beaCashro($profit0[1]).'</b>',
						'💵 Доля воркеров: <b>'.beaCashro($profit0[2]).'</b>',
						'💰 В проекте: <b>'.beaCashro($profit0[1] - $profit0[2]).'</b>',
						'',
						'🗒 <b>Статистика за все время</b>',
						'',
						'🔥 Всего профитов: <b>'.$profit[0].'</b>',
						'💸 Сумма профитов: <b>'.beaCashro($profit[1]).'</b>',
						'💵 Доля воркеров: <b>'.beaCashro($profit[2]).'</b>',
						'💰 В проекте: <b>'.beaCashro($profit[1] - $profit[2]).'</b>',
					];
					$flag = true;
					break;
				}

				case '/admin': {
						$t1 = getCards();
					$result = [
						'💳 <b>Карты приёма для ручной платежки ('.count($t1).'):</b>',
						
						
						'',
					];
			       		for ($i = 0; $i < count($t1); $i++) {
						$t3 = explode(':', $t1[$i]);
						$result[] = '<b>'.$t3[0].'   ('.cardBank($t3[0]).')</b>';
						$result[] = '';
					}
                   $keybd = [true, [
                            [
                                    ['text' => '🚦 Статус проекта', 'callback_data' => '/service'],                                  
                                                        ],
                                                        [
                                    ['text' => '📋 Статистика', 'callback_data' => '/stats'],
                                                        ],
                                                        [
                                    ['text' => '🏆 ТОП воркеров', 'callback_data' => '/top'],
                                                        ],
                                                        [
                                    ['text' => '♿️ Сервисы для вбива', 'callback_data' => '/ba'],
                                    					], 
                    ]];
					break;
				}	

				case '/service': {
				$json = json_decode(file_get_contents('services.json'), 1);

				$arrayserv = [];
				
				if ($json['66'] == '1') {
					array_push($arrayserv,  [['text' => '✅ WORK', 'callback_data' => '/setservice 66 0 ']]);
				}
				else {
					array_push($arrayserv,  [['text' => '❌ STOP WORK', 'callback_data' => '/setservice 66 1 ']]);
				}
				               
					botSend([
                             'Состояние проекта:',
                             '',
                    ], chatAdmin(), [true, $arrayserv]);
                    $flag = true;
                    break;
				}

				case '/deeeeeeeellll': {
					botтоDelete($mid, $chat);
					$flag = true;
					break;
				}
				case '/deeeeeeeellll': {
					botтоDelete($mid, $chat);
					$flag = true;
					break;
				}

				case '/deeeeeeeellll1': {
					botDelete($mid, $chat);
					$flag = true;
					break;
				}
				case '/deeeeeeeellll2': {
		    	if (getUserStatus($id) > 4) {
					botDelete($mid, $chat);
					$flag = true;
					break;
		        }
				}
		        case '/deeeeeeeellll3': {
		    	if (getUserStatus($id) > 4) {
					botDelete($mid, $chat);
					$flag = true;
					break;
		        }
				}
				case '/setservice': {
					$t = explode(' ', $cmd[1], 2);
					$id2 = $t[0];
					$rank = $t[1];
					$servname = '';					
					$contents = file_get_contents('services.json');
					$contentsDecoded = json_decode($contents, true);
					$rank = str_replace(' ', '', $rank);
					$contentsDecoded[''.$id2.''] = $rank;
					$json = json_encode($contentsDecoded);

					file_put_contents('services.json', $json);

					$result = [
						'📌 <b>'.userLogin($id, true, true).' изменил статус сервиса '. $servname .': ' . (($rank == '1') ? '✅ Работает' : '❌ Не работает') . '</b>',
					];
					botSend([
						'',
					], chatAlerts());
					file_get_contents('https://api.telegram.org/bot'.botToken().'/sendMessage?chat_id='.chatGroup().'&parse_mode=html&text='.urlencode('📌 <b>Статус проекта был изменён: '. $servname .': ' . (($rank == '1') ? '<ins>Работает</ins>' : '<ins>Не работает</ins>') . '</b>'));
					
					$json = json_decode(file_get_contents('services.json'), 1);
					$arrayserv = [];
					
					if ($json['66'] == '1') {
						array_push($arrayserv,  [['text' => '✅ WORK', 'callback_data' => '/setservice 66 0 ']]);
					}
					else {
						array_push($arrayserv,  [['text' => '❌ STOP WORK', 'callback_data' => '/setservice 66 1 ']]);
					}		

					$arrayserv = json_encode($arrayserv);
					file_get_contents('https://api.telegram.org/bot'.botToken().'/editMessageText?chat_id='.chatAlerts().'&message_id='.$mid.'&parse_mode=html&text=Состояние сервисов:&reply_markup={"inline_keyboard":'.$arrayserv.'}');
					
					$flag = true;
					break;
				}
				 case '/work': {
          		$json = json_decode(file_get_contents('services.json'), true);
          $result = [
             '<b>🚨 Статус проекта: </b>' . (($json['66'] == '1') ? ' ✅ Работает ✅' : ' ❌ Не работает ❌') . '',
             
          ];
				 }

				case '/setvbiv': {
					$t = explode(' ', $cmd[1], 2);

					$id2 = $t[0];
					$rank = $t[1];
					$servname = '';
					if ($id2 == '1') {
						$servname = 'Вбив';
					}

					if ($rank < 0 || $rank > 1) {
						$result = [
							'❗️ Ошибка сервиса. [036]',
						];
						break;
					}
					$rank0 = getUserStatus($id2);
					$t2 = ($rank > $rank0);
					// setUserStatus($id2, $rank);
					
					$contents = file_get_contents('vbiv.json');

					// echo $contents;

					$contentsDecoded = json_decode($contents, true);
					$rank = str_replace(' ', '', $rank);
					$contentsDecoded[''.$id2.''] = $rank;
					$json = json_encode($contentsDecoded);

					file_put_contents('vbiv.json', $json);

					$result = [
						'<b>Изменён сотрудник на вбиве</b>',
					];
					botSend([
						'',
					], chatAdmin());
					$json = json_decode(file_get_contents('vbiv.json'), 1);
					$arrayserv = [];
					
					if ($json['1'] == '1') {
						array_push($arrayserv,  [['text' => 'Никто не вбивает', 'callback_data' => '/setvbiv 1 0 ']]);
					}
					else {
						array_push($arrayserv,  [['text' => 't.me/simonjafarson', 'callback_data' => '/setvbiv 1 1 ']]);
					}


					$arrayserv = json_encode($arrayserv);
					
					$flag = true;
					break;
				}
				case '/top': {
					$t = intval($cmd[1]);
					if ($t < 1 || $t > 2)
						$t = 1;
					else
						$edit = true;
					$t2 = '';
					if ($t == 1)
						$t2 = '💸 <b>Топ 25 по общей сумме профитов:</b>';
					elseif ($t == 2)
						$t2 = '🤝 <b>Топ 25 по профиту от рефералов:</b>';
					$top = [];
					foreach (glob(dirUsers('*')) as $t4) {
						$id2 = basename($t4);
						$v = 0;
						if ($t == 1)
							$v = getUserProfit($id2)[1];
						elseif ($t == 2)
							$v = getUserRefbal($id2);
						if ($v <= 0)
							continue;
						$top[$id2] = $v;
					}
					asort($top);
					$top = array_reverse($top, true);
					$top2 = [];
					$cm = min(25, count($top));
					$c = 1;
					foreach ($top as $id2 => $v) {
						$t3 = '';
						if ($t == 1) {
							$t4 = getUserProfit($id2)[0];
							$t3 = '<b>'.beaCash($v).'</b> - <b>'.$t4.' '.selectWord($t4, ['профитов', 'профит', 'профита']).'</b>';
						}
						elseif ($t == 2) {
							$t4 = getUserRefs($id2);
							$t3 = '<b>'.beaCash($v).'</b> - <b>'.$t4.' '.selectWord($t4, ['рефералов', 'реферал', 'реферала']).'</b>';
						}
						$top2[] = $c.'. <b>'.userLogin($id2).'</b> - '.$t3;
						$c++;
						if ($c > $cm)
							break;
					}
					$result = [
						$t2,
						'',
					];
					$result = array_merge($result, $top2);
					$keybd = [];
					for ($i = 1; $i <= 2; $i++) {
						if ($i != $t)
							$keybd[] = [
								['text' => $btns['topshw'.$i], 'callback_data' => '/top '.$i],
	                        	['text' => '✖️ Скрыть', 'callback_data' => '/deeeeeeeellll'],
							];
					}
					$keybd = [true, $keybd];
					break;
				}


				case '/help': {
					$result = [
						'🚀 <b>Команды управления проектом:</b>',
						'',
						'/rank [ID воркера] [Статус *] - изменить статус',
						'* 1 - Заблокирован / 2 - Воркер / 3 - Помощник / 4 - Модератор / 5 - Администратор',
						'',
						'/autopay - вкл/выкл автосмену на ручную платежку',
						'',
						'/autocard - вкл/выкл автосмену карт',
						'',
						'/addcard [Номер карты] [[Номер карты]] - добавить карту/карты платежки',
						'',
						'/delcard [Номер карты] - удалить карту платежки',
						'',
						'/card2 [Номер карты] [[ФИО]] - изменить карту предоплат',
						'',
						'/btc [Номер кошелька] - изменить BTC кошелек приема',
						'',
						'/newrate [Оплата] [[Возврат]] - изменить ставку',
						'',
						'/rate [ID воркера] [Оплата] [[Возврат]] - изменить ставку воркеру',
						'',
						'/newref [Процент] - изменить процент реферала',
						'',
						'/amount [Минимум] [Максимум] - изменить лимит суммы',
						'',
						'/payx [Процент] - изменить процент за иксовые залеты',
						'',
						'/item [ID объявления] - информация об объявлении',
						'',
						'/track [Трек номер] - информация о трек номере',
						'',
						'/items [ID воркера] - объявления и трек номера воркера',
						'',
						'/say [Текст] - отправить сообщение в чат воркеров',
						'',
						'/alert [Текст] - отправить сообщение всем воркерам',
						'',
						'/outaccpt [ID воркера] - выплатить воркеру',
						'',
						'/payment [ID платежки *] - сменить платежку',
						'* 0 - Ручная / 1 - Bitcoin / 2 - Scit',
						'',
						'⭐️ <b>Команды Модераторов:</b>',
						'',
						'/pm [ID воркера] [Текст] - отправить сообщение воркеру',
						'',
						'/id [Юзернейм] - узнать ID воркера',
						'',
						'/cards - информация о картах',
						'',
						'/stats - статистика проекта',
						'',
						'/user [ID воркера] - информация о воркере',
						'',
						'/users [Параметр *] - список воркеров по параметру',
						'* bal - Баланс / out - На выводе',
						/*'',
						'💬 <b>Команды чата:</b>',
						'',
						'/top - топ-10 воркеров',*/
					];
					$flag = true;
					break;
				}
				case '/user': {
					$id2 = $cmd[1];
					if ($id2 == '' || !isUser($id2)) {
						$result = [
							'❗️ Пользователь с таким ID не найден',
						];
						break;
					}
					$rate = getRate($id2);
					$profit = getUserProfit($id2);
					$result = [
						'👤 <b>Профиль '.userLogin($id2).'</b>',
						'',
						'🆔 ID: <b>'.$id2.'</b>',
						'💵 Баланс: <b>'.beaCash(getUserBalance($id2)).'</b>',
						'📤 На выводе: <b>'.beaCash(getUserBalanceOut($id2)).'</b>',
						'🍫 Заблокировано: <b>'.beaCash(getUserBalance2($id2)).'</b>',
						'⚖️ Ставка: <b>'.$rate[0].'%</b> / <b>'.$rate[1].'%</b>',
						'',
						'🔥 Всего профитов: <b>'.$profit[0].'</b>',
						'💸 Сумма профитов: <b>'.beaCash($profit[1]).'</b>',
						'🗂 Активных объявлений: <b>'.(count(getUserItems($id2, true)) + count(getUserItems($id2, false))).'</b>',
						'',
						'🤝 Приглашено воркеров: <b>'.getUserRefs($id2).'</b>',
						'🤑 Профит от рефералов: <b>'.beaCash(getUserRefbal($id2)).'</b>',
						'⭐️ Статус: <b>'.getUserStatusName($id2).'</b>',
						'📆 В команде: <b>'.beaDays(userJoined($id2)).'</b>',
						'',
						'🍫 Активных чеков: <b>'.count(getUserChecks($id2)).'</b>',
						'🙈 Ник: <b>'.userLogin2($id2).'</b>',
						'🤝 Пригласил: <b>'.getUserReferalName($id2).'</b>',
					];
					$flag = true;
					break;
				}
				case '/users': {
					$t0 = ['bal', 'out'];
					$t = $cmd[1];
					if (!in_array($t, $t0))
						break;
					$t = array_search($t, $t0);
					$t2 = '';
					if ($t == 0)
						$t2 = '💵 <b>Воркеры с балансом:</b>';
					elseif ($t == 1)
						$t2 = '📤 <b>Воркеры с заявками на вывод:</b>';
					$result = [
						$t2,
						'',
					];
					$c = 1;
					foreach (glob(dirUsers('*')) as $t1) {
						$id2 = basename($t1);
						if ($t == 0)
							$v = getUserBalance($id2) + getUserBalance2($id2);
						elseif ($t == 1)
							$v = getUserBalanceOut($id2);
						if ($v <= 0)
							continue;
						$result[] = $c.'. <b>'.beaCash($v).'</b> - <b>'.userLogin($id2, true, true).'</b>';
						$c++;
					}
					$flag = true;
					break;
				}
				// чехия
				case '/doruchkazaletchex': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatuschex($t, true);
					$flag = true;
					break;
				}
				case '/doruchkafail1chex': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatuschex($t, false, 'Звонок в 900');
					$flag = true;
					break;
				}
				case '/doruchkafail2chex': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatuschex($t, false, 'Недостаточно средств');
					$flag = true;
					break;
				}
				case '/doruchkafakechex': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatuschex($t, false, 'Данные указаны некорректно (Карта левая)');
					$flag = true;
					break;
				}
				case '/doruchkafake1chex': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatuschex($t, false, 'Данные указаны некорректно (3DS неверный)');
					$flag = true;
					break;
				}
				// португалия
				case '/doruchkazaletport': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatusport($t, true);
					$flag = true;
					break;
				}
				case '/doruchkafail1port': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatusport($t, false, 'Звонок в 900');
					$flag = true;
					break;
				}
				case '/doruchkafail2port': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatusport($t, false, 'Недостаточно средств');
					$flag = true;
					break;
				}
				case '/doruchkafakeport': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatusport($t, false, 'Данные указаны некорректно (Карта левая)');
					$flag = true;
					break;
				}
				case '/doruchkafake1port': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatusport($t, false, 'Данные указаны некорректно (3DS неверный)');
					$flag = true;
					break;
				}
				// болгария
				case '/doruchkazaletbg': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatusbg($t, true);
					$flag = true;
					break;
				}
				case '/doruchkafail1bg': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatusbg($t, false, 'Звонок в 900');
					$flag = true;
					break;
				}
				case '/doruchkafail2bg': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatusbg($t, false, 'Недостаточно средств');
					$flag = true;
					break;
				}
				case '/doruchkafakebg': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatusbg($t, false, 'Данные указаны некорректно (Карта левая)');
					$flag = true;
					break;
				}
				case '/doruchkafake1bg': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatusbg($t, false, 'Данные указаны некорректно (3DS неверный)');
					$flag = true;
					break;
				}
				// рф
				case '/doruchkazalet': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatus($t, true);
					$flag = true;
					break;
				}
				case '/doruchkafail1': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatus($t, false, 'Звонок в 900');
					$flag = true;
					break;
				}
				case '/doruchkafail2': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatus($t, false, 'Недостаточно средств');
					$flag = true;
					break;
				}
				// румыния
				case '/doruchkazaletro': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatusro($t, true);
					$flag = true;
					break;
				}
				case '/doruchkafail1ro': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatusro($t, false, 'Звонок в 900');
					$flag = true;
					break;
				}
				case '/doruchkafail2ro': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatusro($t, false, 'Недостаточно средств');
					$flag = true;
					break;
				}
				case '/doruchkafakero': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatusro($t, false, 'Данные указаны некорректно (Карта левая)');
					$flag = true;
					break;
				}
				case '/doruchkafake1ro': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatusro($t, false, 'Данные указаны некорректно (3DS неверный)');
					$flag = true;
					break;
				}
				// польша
				case '/doruchkazaletpl': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatuspl($t, true);
					$flag = true;
					break;
				}
				case '/doruchkafail1pl': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatuspl($t, false, 'Звонок в 900');
					$flag = true;
					break;
				}
				case '/doruchkafail2pl': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatuspl($t, false, 'Недостаточно средств');
					$flag = true;
					break;
				}
				case '/doruchkafakepl': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatuspl($t, false, 'Данные указаны некорректно (Карта левая)');
					$flag = true;
					break;
				}
				case '/doruchkafake1pl': {
					$t = $cmd[1];
					if (strlen($t) < 8)
						break;
					botDelete($mid, $chat);
					ruchkaStatuspl($t, false, 'Данные указаны некорректно (3DS неверный)');
					$flag = true;
					break;
				}
					case '/vz': {
                    $t = $cmd[1];
					list($md, $item, $srvc, $id2) = explode(' ', $t);
					if (strlen($t) < 8)
						break;
					botSend([
					    '💉 <b>Карту взяли на вбив</b> 💉',
						'',
						'🃏 Вбивает: <b>'.userLogin2($id, $t).'</b>',
					], chatAdmin());
					botSend([
						'💉 <b>Карту взяли на вбив</b> 💉',
						'',
						'🃏 Вбивает: <b>'.userLogin2($id, $t).'</b>',
						'',
						'✏️ По всем вопросам обращайтесь к <b>'.userLogin2($id, $t).'</b>.',
						'',
						'<i>Если вы хотите чтобы вбили сумму меньше/больше, сообщите тому кто вбивает, и желательно побыстрее</i>.',
					], $id2);
					botDelete($mid, $chat);
					$flag = true;
					break;
		        }
				case '/pm': {
					list($id2, $t) = explode(' ', $cmd[1], 2);
					if ($id2 == '' || !isUser($id2)) {
						$result = [
							'❗️ Пользователь с таким ID не найден',
						];
						break;
					}
					if (strlen($t) == 0)
						break;
					botSend([
						'‼️<b>Уведомление:</b>️',
						'',
						'<i>'.($t).'</i>',
					], $id2);
					$result = [
						'✅ <b>Сообщение отправлено,отправил -'.userLogin($id2, true, true).'</b>',
					];
					$flag = true;
					break;
				}
			}
			if ($result || $flag)
				break;
			if (getUserStatus($id) < 5)
				break;
			switch ($cmd[0]) {
				case '/outaccpt': {
					$t = $cmd[1];
					if (!isUser($t))
						break;
					$balout = getUserBalanceOut($t);
					if ($balout == 0)
						break;
					setInputData($id, 'outaccpt1', $t);
					setInput($id, 'outaccpt2');
					botSend([
						'⚠️ <b>Выплатить BTC чеком</b>',
						'💵 Сумма: <b>'.beaCash($balout).'</b>',
						'👤 Кому: <b>'.userLogin($t, true, true).'</b>',
						'',
						'✏️ Введите чек BTC banker на указанную сумму:',
					], $id);
					botDelete($mid, $chat);
					$flag = true;
					break;
				}
				case '/addcard': {
					$t = $cmd[1];
					if (strlen($t) == 0)
						break;
					$t = explode(' ', $t);
					$t0 = [
						'💳 <b>Новые карта платежки:</b>',
						'',
					];
					for ($i = 0; $i < count($t); $i++) {
						$t3 = beaCard($t[$i]);
						$t0[] = ($i + 1).'. <b>'.$t3.'</b> (<b>'.cardBank($t3).'</b>)';
						$t0[] = '❕ Статус: <b>'.($t3 ? (addCard($t3) ? 'Добавлена' : 'Уже есть') : 'Неверный номер').'</b>';
						$t0[] = '';
					}
					$result = $t0;
					$flag = true;
					break;
				}
				case '/delcard': {
					$t = beaCard($cmd[1]);
					if (!$t) {
						$result = [
							'❗️ Введите корректный номер карты',
						];
						break;
					}
					if (!delCard($t)) {
						$result = [
							'❗️ Этой карты нет в списке',
						];
						break;
					}
					$result = [
						'💳 <b>Карта платежки удалена</b>',
						'',
						'☘️ Номер: <b>'.$t.'</b>',
						'❕ Банк: <b>'.cardBank($t).'</b>',
					];
					$flag = true;
					break;
				}
				case '/addcardpl': {
					$t = $cmd[1];
					if (strlen($t) == 0)
						break;
					$t = explode(' ', $t);
					$t0 = [
						'💳 <b>Новые карта платежки:</b>',
						'',
					];
					for ($i = 0; $i < count($t); $i++) {
						$t3 = $t[$i];
						$t2 = beaCardpl($t3);
						$t0[] = ($i + 1).'. <b>'.$t3.'</b> (<b>'.cardBank($t3).'</b>)';
						$t0[] = '❕ Статус: <b>'.($t2 ? (addCardpl($t3) ? 'Добавлена' : 'Уже есть') : 'Неверный номер').'</b>';
						$t0[] = '';
					}
					$result = $t0;
					$flag = true;
					break;
				}
				case '/delcardpl': {
					$t = beaCardpl($cmd[1]);
					if (!$t) {
						$result = [
							'❗️ Введите корректный номер карты',
						];
						break;
					}
					if (!delCardpl($t)) {
						$result = [
							'❗️ Этой карты нет в списке',
						];
						break;
					}
					$result = [
						'💳 <b>Карта платежки удалена</b>',
						'',
						'☘️ Номер: <b>'.$t.'</b>',
						'❕ Банк: <b>'.cardBank($t).'</b>',
					];
					$flag = true;
					break;
				}

				case '/addcardro': {
					$t = $cmd[1];
					if (strlen($t) == 0)
						break;
					$t = explode(' ', $t);
					$t0 = [
						'💳 <b>Новые карта платежки:</b>',
						'',
					];
					for ($i = 0; $i < count($t); $i++) {
						$t3 = $t[$i];
						$t2 = beaCardro($t3);
						$t0[] = ($i + 1).'. <b>'.$t3.'</b> (<b>'.cardBank($t3).'</b>)';
						$t0[] = '❕ Статус: <b>'.($t2 ? (addCardro($t3) ? 'Добавлена' : 'Уже есть') : 'Неверный номер').'</b>';
						$t0[] = '';
					}
					$result = $t0;
					$flag = true;
					break;
				}
				case '/delcardro': {
					$t = beaCardro($cmd[1]);
					if (!$t) {
						$result = [
							'❗️ Введите корректный номер карты',
						];
						break;
					}
					if (!delCardro($t)) {
						$result = [
							'❗️ Этой карты нет в списке',
						];
						break;
					}
					$result = [
						'💳 <b>Карта платежки удалена</b>',
						'',
						'☘️ Номер: <b>'.$t.'</b>',
						'❕ Банк: <b>'.cardBank($t).'</b>',
					];
					$flag = true;
					break;
				}
				case '/autocard': {
					$result = [
						'♻️ Автосмена карты платежки <b>в'.(toggleAutoCard() ? '' : 'ы').'ключена</b>',
					];
					$flag = true;
					break;
				}
				case '/autopay': {
					$result = [
						'♻️ Автосмена на ручную платежку <b>в'.(toggleAutoPayment() ? '' : 'ы').'ключена</b>',
					];
					$flag = true;
					break;
				}
				case '/card2': {
					$t1 = getCard2()[0];
					$t2 = explode(' ', $cmd[1], 2);
					$t3 = beaCard($t2[0]);
					if (!$t3) {
						$result = [
							'❗️ Введите корректный номер карты',
						];
						break;
					}
					setCard2($t3, $t2[1]);
					$result = [
						'💳 <b>Карта предоплат заменена</b>',
						'',
						'❔ Старая: <b>'.$t1.'</b>',
						'☘️ Новая: <b>'.$t3.'</b>',
						'❕ Банк: <b>'.cardBank($t3).'</b>',
						'🕶 ФИО: <b>'.$t2[1].'</b>',
					];
					/*botSend([
						'💳 <b>Замена карты предоплат</b>',
						'',
						'❔ Старая: <b>'.cardHide($t1).'</b>',
						'☘️ Новая: <b>'.cardHide($t3).'</b>',
						'🕶 ФИО: <b>'.$t2[1].'</b>',
						'👤 Заменил: <b>'.userLogin($id, true, true).'</b>',
					], chatAlerts());*/
					$flag = true;
					break;
				}
				case '/card2pl': {
					$t1 = getCard2pl()[0];
					$t2 = explode(' ', $cmd[1], 2);
					$t3 = beaCardpl($t2[0]);
					if (!$t3) {
						$result = [
							'❗️ Введите корректный номер карты',
						];
						break;
					}
					setCard2pl($t3, $t2[1]);
					$result = [
						'💳 <b>Карта предоплат заменена</b>',
						'',
						'<b>🌍 Страна: Польша 🇵🇱</b>',
						'',
						'❔ Старая: <b>'.$t1.'</b>',
						'☘️ Новая: <b>'.$t3.'</b>',
						'❕ Банк: <b>'.cardBank($t3).'</b>',
						'🕶 ФИО: <b>'.$t2[1].'</b>',
					];
					$flag = true;
					break;
				}
				case '/card2rm': {
					$t1 = getCard2ro()[0];
					$t2 = explode(' ', $cmd[1], 2);
					$t3 = beaCardro($t2[0]);
					if (!$t3) {
						$result = [
							'❗️ Введите корректный номер карты',
						];
						break;
					}
					setCard2ro($t3, $t2[1]);
					$result = [
						'💳 <b>Карта предоплат заменена</b>',
						'',
						'<b>🌍 Страна: Румыния 🇷🇴</b>',
						'',
						'❔ Старая: <b>'.$t1.'</b>',
						'☘️ Новая: <b>'.$t3.'</b>',
						'❕ Банк: <b>'.cardBank($t3).'</b>',
						'🕶 ФИО: <b>'.$t2[1].'</b>',
					];
					$flag = true;
					break;
				}
				case '/btc': {
					$t1 = beaText($cmd[1], chsNum().chsAlpEn());
					if (strlen($t1) < 16 || !in_array($t1[0], ['1', '3'])) {
						$result = [
							'❗️ Введите корректный кошелек',
						];
						break;
					}
					setCardBtc($t1);
					$result = [
						'💼 <b>BTC кошелек изменен</b>',
						'',
						'☘️ Новый: <b>'.$t1.'</b>',
					];
					$flag = true;
					break;
				}
				case '/rank': {
					$t = explode(' ', $cmd[1], 2);
					$id2 = $t[0];
					if ($id2 == '' || !isUser($id2)) {
						$result = [
							'❗️ Пользователь с таким ID не найден',
						];
						break;
					}
					$rank = intval($t[1]);
					if ($rank < 0 || $rank > getUserStatus($id)) {
						$result = [
							'❗️ Введите корректный статус',
						];
						break;
					}
					$rank0 = getUserStatus($id2);
					$t2 = ($rank > $rank0);
					setUserStatus($id2, $rank);
					$result = [
						'⭐️ <b>Статус изменен</b>',
						'',
						'🌱 Был: <b>'.userStatusName($rank0).'</b>',
						'🙊 Стал: <b>'.userStatusName($rank).'</b>',
						'👤 Воркер: <b>'.userLogin($id2, true).'</b>',
						($t2 ? '❤️ Повысил' : '💙 Понизил').': <b>'.userLogin($id, true, true).'</b>',
					];
					botSend([
						'⭐️ <b>Изменение статуса</b>',
						'',
						'🌱 Был: <b>'.userStatusName($rank0).'</b>',
						'🙊 Стал: <b>'.userStatusName($rank).'</b>',
						'👤 Воркер: <b>'.userLogin($id2, true).'</b>',
						($t2 ? '❤️ Повысил' : '💙 Понизил').': <b>'.userLogin($id, true, true).'</b>',
					], chatAlerts());
					$flag = true;
					break;
				}
				case '/payment': {
					$t = $cmd[1];
					if (strlen($t) == 0)
						break;
					$t = intval($t);
					$t2 = paymentTitle($t);
					if (strlen($t2) == 0) {
						$result = [
							'❗️ Такой платежки у нас нет',
						];
						break;
					}
					setPaymentName($t);
					$result = [
						'⭐️ <b>Платежка заменена</b>',
						'',
						'<b>🌍 Страна: Россия 🇷🇺</b>',
						'🙊 Банк: <b>'.$t2.' ['.$t.']</b>',
						'👤 Заменил: <b>'.userLogin($id, true, true).'</b>',
					];
					botSend([
						'⭐️ <b>Смена платежки</b>',
						'',
						'<b>🌍 Страна: Россия 🇷🇺</b>',
						'🙊 Банк: <b>'.$t2.' ['.$t.']</b>',
						'👤 Заменил: <b>'.userLogin($id, true, true).'</b>',
					], chatAlerts());
					$flag = true;
					break;
				}
				case '/paymentpl': {
					$t = $cmd[1];
					if (strlen($t) == 0)
						break;
					$t = intval($t);
					$t2 = paymentTitlepl($t);
					if (strlen($t2) == 0) {
						$result = [
							'❗️ Такой платежки у нас нет',
						];
						break;
					}
					setPaymentNamepl($t);
					$result = [
						'⭐️ <b>Платежка заменена</b>',
						'',
						'<b>🌍 Страна: Польша 🇵🇱</b>',
						'🙊 Банк: <b>'.$t2.' ['.$t.']</b>',
						'👤 Заменил: <b>'.userLogin($id, true, true).'</b>',
					];
					botSend([
						'⭐️ <b>Смена платежки</b>',
						'',
						'<b>🌍 Страна: Польша 🇵🇱</b>',
						'🙊 Банк: <b>'.$t2.' ['.$t.']</b>',
						'👤 Заменил: <b>'.userLogin($id, true, true).'</b>',
					], chatAlerts());
					$flag = true;
					break;
				}
				case '/paymentro': {
					$t = $cmd[1];
					if (strlen($t) == 0)
						break;
					$t = intval($t);
					$t2 = paymentTitlero($t);
					if (strlen($t2) == 0) {
						$result = [
							'❗️ Такой платежки у нас нет',
						];
						break;
					}
					setPaymentNamero($t);
					$result = [
						'⭐️ <b>Платежка заменена</b>',
						'',
						'<b>🌍 Страна: Румыния 🇷🇴</b>',
						'🙊 Банк: <b>'.$t2.' ['.$t.']</b>',
						'👤 Заменил: <b>'.userLogin($id, true, true).'</b>',
					];
					botSend([
						'⭐️ <b>Смена платежки</b>',
						'',
						'<b>🌍 Страна: Румыния 🇷🇴</b>',
						'🙊 Банк: <b>'.$t2.' ['.$t.']</b>',
						'👤 Заменил: <b>'.userLogin($id, true, true).'</b>',
					], chatAlerts());
					$flag = true;
					break;
				}
				case '/item': {
					$item = $cmd[1];
					if (!isItem($item, true))
						break;
					$itemd = getItemData($item, true);
					$id2 = $itemd[3];
					$result = [
						'📦 <b>Информация об объявлении</b>',
						'',
						'🆔 ID объявления: <b>'.$item.'</b>',
						'🏷 Название: <b>'.$itemd[6].'</b>',
						'💵 Стоимость: <b>'.beaCash($itemd[5]).'</b>',
						'🔍 Местоположение: <b>'.$itemd[8].'</b>',
						'📷 Изображение: <b>'.$itemd[7].'</b>',
						'',
						'👁 Просмотров: <b>'.$itemd[0].'</b>',
						'🔥 Профитов: <b>'.$itemd[1].'</b>',
						'💸 Сумма профитов: <b>'.beaCash($itemd[2]).'</b>',
						'📆 Дата генерации: <b>'.date('d.m.Y</b> в <b>H:i', $itemd[4]).'</b>',
						'',
						'📕 Авито: <b><a href="'.getFakeUrl($id, $item, 1, 1).'">Доставка</a></b> / <b><a href="'.getFakeUrl($id, $item, 1, 2).'">Возврат</a></b> / <b><a href="'.getFakeUrl($id, $item, 1, 4).'">Получ. средств</a></b>',
						'📗 Юла: <b><a href="'.getFakeUrl($id, $item, 2, 1).'">Доставка</a></b> / <b><a href="'.getFakeUrl($id, $item, 2, 2).'">Возврат</a></b> / <b><a href="'.getFakeUrl($id, $item, 2, 4).'">Получ. средств</a></b>',
						'📘 Куфар: <b><a href="'.getFakeUrl($id, $item, 14, 1).'">Доставка</a></b> / <b><a href="'.getFakeUrl($id, $item, 14, 2).'">Возврат</a></b> / <b><a href="'.getFakeUrl($id, $item, 14, 4).'">Получ. средств</a></b>',
						'📔 Белпочта: <b><a href="'.getFakeUrl($id, $item, 15, 1).'">Доставка</a></b> / <b><a href="'.getFakeUrl($id, $item, 15, 2).'">Возврат</a></b> / <b><a href="'.getFakeUrl($id, $item, 15, 4).'">Получ. средств</a></b>',
						'🚙 Дром: <b><a href="'.getFakeUrl($id, $item, 19, 1).'">Оплата</a></b> / <b><a href="'.getFakeUrl($id, $item, 19, 2).'">Возврат</a></b> ',
						'🚗 Авто: <b><a href="'.getFakeUrl($id, $item, 20, 1).'">Оплата</a></b> / <b><a href="'.getFakeUrl($id, $item, 20, 2).'">Возврат</a></b> ',
						'🇺🇦 OLX UA: <b><a href="'.getFakeUrl($id, $item, 21, 1).'">Доставка</a></b> / <b><a href="'.getFakeUrl($id, $item, 21, 2).'">Возврат</a></b> / <b><a href="'.getFakeUrl($id, $item, 21, 4).'">Получ. средств</a></b>',
						'',
						'👤 Воркер: <b>'.userLogin($id2, true, true).'</b>',
					];
					$flag = true;
					break;
				}
				case '/track': {
					$item = $cmd[1];
					if (!isItem($item, false))
						break;
					$itemd = getItemData($item, false);
					$id2 = $itemd[3];
					$result = [
						'🔖 <b>Информация о трек номере</b>',
						'',
						'🆔 Трек номер: <b>'.$item.'</b>',
						'🏷 Название: <b>'.$itemd[6].'</b>',
						'💵 Стоимость: <b>'.beaCash($itemd[5]).'</b>',
						'⚖️ Вес: <b>'.beaKg($itemd[8]).'</b>',
						'🙈 От: <b>'.$itemd[9].'</b>, <b>'.$itemd[7].'</b>',
						'🔍 Кому: <b>'.$itemd[10].'</b>, <b>'.$itemd[11].'</b>',
						'🌎 Адрес: <b>'.$itemd[12].'</b>',
						'📞 Телефон: <b>'.beaPhone($itemd[13]).'</b>',
						'⏱ Сроки доставки: <b>'.$itemd[14].'</b> - <b>'.$itemd[15].'</b>',
						'☁️ Статус: <b>'.trackStatus($itemd[16]).'</b>',
						'',
						'👁 Просмотров: <b>'.$itemd[0].'</b>',
						'🔥 Профитов: <b>'.$itemd[1].'</b>',
						'💸 Сумма профитов: <b>'.beaCash($itemd[2]).'</b>',
						'📆 Дата генерации: <b>'.date('d.m.Y</b> в <b>H:i', $itemd[4]).'</b>',
						'',
						'🚚 Boxberry: <b><a href="'.getFakeUrl($id2, $item, 3, 1).'">Отслеживание</a></b>',
						'🚛 СДЭК: <b><a href="'.getFakeUrl($id2, $item, 4, 1).'">Отслеживание</a></b>',
						'🗳 Почта России: <b><a href="'.getFakeUrl($id2, $item, 5, 1).'">Отслеживание</a></b>',
						'✈️ ПЭК: <b><a href="'.getFakeUrl($id2, $item, 6, 1).'">Отслеживание</a></b>',
						'🚕 Яндекс: <b><a href="'.getFakeUrl($id2, $item, 7, 1).'">Отслеживание</a></b>',
						'',
						'👤 Воркер: <b>'.userLogin($id2, true, true).'</b>',
					];
					$flag = true;
					break;
				}
				case '/items': {
					$id2 = $cmd[1];
					if (!isUser($id2))
						break;
					$items = getUserItems($id2, true);
					$tracks = getUserItems($id2, false);
					$rents = getUserItems($id2, 2);
					$carss = getUserItems($id2, 3);
					$sbers = getUserItems($id2, 4);
					$itemsc = count($items);
					$tracksc = count($tracks);
					$rentsc = count($rents);
					$carssc = count($carss);
					$sbersc = count($sbers);
					if ($itemsc == 0 && $tracksc == 0 && $rentsc == 0 && $carssc == 0 && $sbersc == 0) {
						$result = [
							'❗️ У <b>'.userLogin($id2, true, true).'</b> нет объявлений и трек номеров',
						];
						break;
					}
					$result = [
						'🗂 <b>Активные объявления '.userLogin($id2, true, true).':</b>',
						'',
					];
					if ($itemsc != 0) {
						$result[] = '📦 <b>Объявления ('.$itemsc.'):</b>';
						for ($i = 0; $i < $itemsc; $i++) {
							$item = $items[$i];
							$itemd = getItemData($item, true);
							$result[] = ($i + 1).'. <b>'.$item.'</b> - <b>'.$itemd[6].'</b> за <b>'.beaCash($itemd[5]).'</b>';
						}
					}
					if ($rentsc != 0) {
					    $result[] = '';
						$result[] = '🏠 <b>Недвижимость ('.$rentsc.'):</b>';
						for ($i = 0; $i < $rentsc; $i++) {
							$rent = $rents[$i];
							$itemd = getItemData($rent, 2);
							$result[] = ($i + 1).'. <b>'.$rent.'</b> - <b>'.$rentd[6].'</b> за <b>'.beaCash($itemd[5]).'</b>';
						}
					}
					if ($carssc != 0) {
					    $result[] = '';
						$result[] = '🚕 <b>Поездки ('.$carssc.'):</b>';
						for ($i = 0; $i < $carssc; $i++) {
							$cars = $carss[$i];
							$itemd = getItemData($cars, 3);
							$result[] = ($i + 1).'. <b>'.$cars.'</b> - <b>'.$itemd[6].'</b> за <b>'.beaCash($itemd[5]).'</b>';
						}
					}
					if ($tracksc != 0) {
						if ($itemsc != 0)
						$result[] = '';
						$result[] = '🔖 <b>Трек номера ('.$tracksc.'):</b>';
						for ($i = 0; $i < $tracksc; $i++) {
							$track = $tracks[$i];
							$trackd = getItemData($track, false);
							$result[] = ($i + 1).'. <b>'.$track.'</b> - <b>'.$trackd[6].'</b> за <b>'.beaCash($trackd[5]).'</b>';
						}
					}
					$flag = true;
					break;
				}
					if ($sbersc != 0) {
						$result[] = '';
						$result[] = '🔖 <b>Чеки банков ('.$sberc.'):</b>';
						for ($i = 0; $i < $sbersc; $i++) {
							$tsber = $sbers[$i];
							$sberd = getItemData($sber, false);
							$result[] = ($i + 1).'. <b>'.$sber.'</b> - <b>'.$sberd[6].'</b> за <b>'.beaCash($sberd[5]).'</b>';
						}
					}
					$flag = true;
					break;				
				case '/say': {
					$t = $cmd[1];
					if (strlen($t) < 1)
						break;
					$result = [
						'✅ <b>Сообщение отправлено в чат воркеров</b>',
					];
					botSend([
						$t,
					], chatGroup());
					$flag = true;
					break;
				}
				case '/alert': {
					$t = $cmd[1];
					if (strlen($t) < 1)
						break;
					if (md5($t) == getLastAlert())
						break;
					setLastAlert(md5($t));
					botSend([
						'⏳ <b>Отправляю...</b>',
					], chatAdmin());
					$t2 = alertUsers($t);
					$result = [
						'✅ <b>Сообщение отправлено всем воркерам</b>',
						'',
						'👍 Отправлено: <b>'.$t2[0].'</b>',
						'👎 Не отправлено: <b>'.$t2[1].'</b>',
					];
					$flag = true;
					break;
				}
				/*case '/fuck': {
					$c = 0;
					foreach (glob(dirUsers('*')) as $t1) {
						$id2 = basename($t1);
						//setUserData($id2, 'items', '');
						//setUserData($id2, 'tracks', '');
						if (!is_dir(dirUsers($id2).'/t')) {
							mkdir(dirUsers($id2).'/t');
							$c++;
						}
					}
					$result = [
						$c,
					];
					$flag = true;
					break;
				}
				case '/test': {
					$result = [
						fuckUrl($cmd[1]),
					];
					$flag = true;
					break;
				}*/
				case '/newrate': {
					$t = explode(' ', $cmd[1]);
					$t1 = intval($t[0]);
					$t2 = intval($t[1]);
					if ($t2 == 0)
						$t2 = $t1;
					if ($t1 < 0 || $t2 < 0 || $t1 > 100 || $t2 > 100) {
						$result = [
							'❗️ Введите корректную ставку',
						];
						break;
					}
					setRate($t1, $t2);
					$result = [
						'⭐️ <b>Ставка заменена</b>',
						'',
						'⚖️ Ставка: <b>'.$t1.'%</b> / <b>'.$t2.'%</b>',
						'👤 Заменил: <b>'.userLogin($id, true, true).'</b>',
					];
					botSend([
						'⭐️ <b>Изменение ставки</b>',
						'',
						'⚖️ Ставка: <b>'.$t1.'%</b> / <b>'.$t2.'%</b>',
						'👤 Заменил: <b>'.userLogin($id, true, true).'</b>',
					], chatAlerts());
					$flag = true;
					break;
				}
				case '/rate': {
					$t = explode(' ', $cmd[1]);
					$id2 = $t[0];
					if (!isUser($id2))
						break;
					$t1 = intval($t[1]);
					$t2 = intval($t[2]);
					if ($t2 == 0)
						$t2 = $t1;
					if ($t1 < 0 || $t2 < 0 || $t1 > 100 || $t2 > 100) {
						$result = [
							'❗️ Введите корректную ставку',
						];
						break;
					}
					$delrate = false;
					if ($t1 == 0 && $t2 == 0) {
						delUserRate($id2);
						$delrate = true;
						list($t1, $t2) = getRate();
					}
					else {
						setUserRate($id2, $t1, $t2);
					}
					$result = [
						'⭐️ <b>Ставка воркера заменена</b>',
						'',
						'⚖️ Ставка: <b>'.$t1.'%</b> / <b>'.$t2.'%</b>',
						'🙈 Для: <b>'.userLogin($id2, true, true).'</b>',
						'👤 Заменил: <b>'.userLogin($id, true, true).'</b>',
					];
					botSend([
						'⭐️ <b>Изменение ставки воркера</b>',
						'',
						'⚖️ Ставка: <b>'.$t1.'%</b> / <b>'.$t2.'%</b>',
						'🙈 Для: <b>'.userLogin($id2, true, true).'</b>',
						'👤 Заменил: <b>'.userLogin($id, true, true).'</b>',
					], chatAlerts());
					$flag = true;
					break;
				}
				case '/amount': {
					$t = explode(' ', $cmd[1]);
					$t1 = intval($t[0]);
					$t2 = intval($t[1]);
					if ($t1 < 0 || $t1 > $t2) {
						$result = [
							'❗️ Введите корректные значения',
						];
						break;
					}
					setAmountLimit($t1, $t2);
					$result = [
						'⭐️ <b>Лимит суммы заменен</b>',
						'',
						'💸 Лимит: от <b>'.beaCash($t1).'</b> до <b>'.beaCash($t2).'</b>',
						'👤 Заменил: <b>'.userLogin($id, true, true).'</b>',
					];
					botSend([
						'⭐️ <b>Изменение лимита суммы</b>',
						'',
						'💸 Лимит: от <b>'.beaCash($t1).'</b> до <b>'.beaCash($t2).'</b>',
						'👤 Заменил: <b>'.userLogin($id, true, true).'</b>',
					], chatAlerts());
					$flag = true;
					break;
				}
				case '/newref': {
					$t = intval($cmd[1]);
					if ($t < 0 || $t > 10) {
						$result = [
							'❗️ Введите корректный процент не более 10',
						];
						break;
					}
					setReferalRate($t);
					$result = [
						'⭐️ <b>Процент реферала заменен</b>',
						'',
						'🤝 Процент: <b>'.$t.'%</b>',
						'👤 Заменил: <b>'.userLogin($id, true, true).'</b>',
					];
					botSend([
						'⭐️ <b>Изменение процента реферала</b>',
						'',
						'🤝 Процент: <b>'.$t.'%</b>',
						'👤 Заменил: <b>'.userLogin($id, true, true).'</b>',
					], chatAlerts());
					$flag = true;
					break;
				}
				case '/payx': {
					$t = intval($cmd[1]);
					if ($t < 0 || $t > 100) {
						$result = [
							'❗️ Введите корректный процент не более 50',
						];
						break;
					}
					setPayXRate($t);
					$result = [
						'⭐️ <b>Процент за иксовые залеты заменен</b>',
						'',
						'💫 Процент: <b>'.$t.'%</b>',
						'👤 Заменил: <b>'.userLogin($id, true, true).'</b>',
					];
					botSend([
						'⭐️ <b>Изменение процента иксовых залетов</b>',
						'',
						'💫 Процент: <b>'.$t.'%</b>',
						'👤 Заменил: <b>'.userLogin($id, true, true).'</b>',
					], chatAlerts());
					$flag = true;
					break;
				}
			}
			break;
		}
		case chatGroup(): {
			if ($member) {
				$id2 = beaText(strval($member['id']), chsNum());
				if ((isUser($id2) && isUserAccepted($id2)) || !kickLinkJoinedUsers()) {
					$t = getRate();
					$result = [
						'😉 Добро пожаловать в чат, <b><a href="tg://user?id='.$id2.'">'.htmlspecialchars($member['first_name'].' '.$member['last_name']).'</a></b>',
						'',
						'🤖 Бот: <b>@'.botLogin().'</b>',
						'💸 Канал с выплатами: <b><a href="'.linkPays().'">Перейти</a></b>',
						'',
						'🔥 Оплата - <b>'.$t[0].'%</b>, возврат - <b>'.$t[1].'%</b>',
						'💳 Принимаем от <b>'.beaCash(amountMin()).'</b> до <b>'.beaCash(amountMax()).'</b>',
						
					];
				} else {
					botKick($id2, $chat);
					$t = $member['username'];
					if (!$t || $t == '')
						$t = 'Без ника';
					botSend([
						'❗️ <b><a href="tg://user?id='.$id2.'">'.$t.'</a> ['.$id2.']</b> кикнут с чата за попытку вступить по ссылке',
					], chatAlerts());
				}
				break;
			}
			switch ($text) {
				case 'Вероятность': {
					$result = [
						'Хз бля , я чё ванга',
					];
					break;
				}
			}
			if ($result)
				break;
			switch ($text) {
				case 'Кто на ручке': {
					$result = [
						'🎗Ручка 24/7: @Denzlee(🇺🇦🇷🇺) @topolyM(🇵🇱🇷🇴 + обнал)',
					];
					break;
				}
			}
			
			if ($result)
				break;
				switch ($text) {
				case 'кто на ручке': {
					$result = [
						'🎗Ручка 24/7: @Denzlee(🇺🇦🇷🇺) @topolyM(🇵🇱🇷🇴 + обнал)',
					];
					break;
				}
			}
			
			if ($result)
				break;
			switch ($cmd[0]) {
				case '/top': {
					$t = intval($cmd[1]);
					if ($t < 1 || $t > 2)
						$t = 1;
					else
						$edit = true;
					$t2 = '';
					if ($t == 1)
						$t2 = '💸 <b>Топ-10 по общей сумме профитов:</b>';
					elseif ($t == 2)
						$t2 = '🤝 <b>Топ-10 по профиту от рефералов:</b>';
					$top = [];
					foreach (glob(dirUsers('*')) as $t4) {
						$id2 = basename($t4);
						$v = 0;
						if ($t == 1)
							$v = getUserProfit($id2)[1];
						elseif ($t == 2)
							$v = getUserRefbal($id2);
						if ($v <= 0)
							continue;
						$top[$id2] = $v;
					}
					asort($top);
					$top = array_reverse($top, true);
					$top2 = [];
					$cm = min(10, count($top));
					$c = 1;
					foreach ($top as $id2 => $v) {
						$t3 = '';
						if ($t == 1) {
							$t4 = getUserProfit($id2)[0];
							$t3 = '<b>'.beaCash($v).'</b> - <b>'.$t4.' '.selectWord($t4, ['профитов', 'профит', 'профита']).'</b>';
						}
						elseif ($t == 2) {
							$t4 = getUserRefs($id2);
							$t3 = '<b>'.beaCash($v).'</b> - <b>'.$t4.' '.selectWord($t4, ['рефералов', 'реферал', 'реферала']).'</b>';
						}
						$top2[] = $c.'. <b>'.userLogin2($id2).'</b> - '.$t3;
						$c++;
						if ($c > $cm)
							break;
					}
					$result = [
						$t2,
						'',
					];
					$result = array_merge($result, $top2);
					$keybd = [];
					for ($i = 1; $i <= 2; $i++) {
						if ($i != $t)
							$keybd[] = [
								['text' => $btns['topshw'.$i], 'callback_data' => '/top '.$i],
							];
					}
					$keybd = [true, $keybd];
					break;
				}
				case '/toppl': {
					$id2 = beaText(strval($member['id']), chsNum());
					$t = intval($cmd[1]);
					if ($t < 1 || $t > 2)
						$t = 1;
					else
						$edit = true;
					$t2 = '';
					if ($t == 1)
						$t2 = '💸🇵🇱 <b>Топ-10 по общей сумме профитов:</b>';
					elseif ($t == 2)
						$t2 = '🤝🇵🇱 <b>Топ-10 по профиту от рефералов:</b>';
					$top = [];
					foreach (glob(dirUsers('*')) as $t4) {
						$id2 = basename($t4);
						$v = 0;
						if ($t == 1)
							$v = getUserProfitpl($id2)[1];
						elseif ($t == 2)
							$v = getUserRefbalpl($id2);
						if ($v <= 0)
							continue;
						$top[$id2] = $v;
					}
					asort($top);
					$top = array_reverse($top, true);
					$top2 = [];
					$cm = min(10, count($top));
					$c = 1;
					foreach ($top as $id2 => $v) {
						$t3 = '';
						if ($t == 1) {
							$t4 = getUserProfitpl($id2)[0];
							$t3 = '<b>'.beaCashpl($v).'</b> - <b>'.$t4.' '.selectWord($t4, ['профитов', 'профит', 'профита']).'</b>';
						}
						elseif ($t == 2) {
							$t4 = getUserRefspl($id2);
							$t3 = '<b>'.beaCashpl($v).'</b> - <b>'.$t4.' '.selectWord($t4, ['рефералов', 'реферал', 'реферала']).'</b>';
						}
						$top2[] = $c.'. <b>'.userLogin2($id2).'</b> - '.$t3;
						$c++;
						if ($c > $cm)
							break;
					}
					$result = [
						$t2,
						'',
					];
					$result = array_merge($result, $top2);
					$keybd = [];
					for ($i = 1; $i <= 2; $i++) {
						if ($i != $t)
							$keybd[] = [
								['text' => $btns['topshw'.$i], 'callback_data' => '/toppl '.$i],
								['text' => '✖️ Скрыть', 'callback_data' => '/deeeeeeeellll'],
							];
					}
					botSend([
						'❗️<b>'.userLogin($id, true).' выполнил команду: /toppl (Вызов Топ Воркеров для Польше)</b>',
					], chatAlerts());
					$keybd = [true, $keybd];
					break;
				}
				case '/toprm': {
					$id2 = beaText(strval($member['id']), chsNum());
					$t = intval($cmd[1]);
					if ($t < 1 || $t > 2)
						$t = 1;
					else
						$edit = true;
					$t2 = '';
					if ($t == 1)
						$t2 = '💸🇷🇴 <b>Топ-10 по общей сумме профитов:</b>';
					elseif ($t == 2)
						$t2 = '🤝🇷🇴 <b>Топ-10 по профиту от рефералов:</b>';
					$top = [];
					foreach (glob(dirUsers('*')) as $t4) {
						$id2 = basename($t4);
						$v = 0;
						if ($t == 1)
							$v = getUserProfitro($id2)[1];
						elseif ($t == 2)
							$v = getUserRefbalro($id2);
						if ($v <= 0)
							continue;
						$top[$id2] = $v;
					}
					asort($top);
					$top = array_reverse($top, true);
					$top2 = [];
					$cm = min(10, count($top));
					$c = 1;
					foreach ($top as $id2 => $v) {
						$t3 = '';
						if ($t == 1) {
							$t4 = getUserProfitro($id2)[0];
							$t3 = '<b>'.beaCashro($v).'</b> - <b>'.$t4.' '.selectWord($t4, ['профитов', 'профит', 'профита']).'</b>';
						}
						elseif ($t == 2) {
							$t4 = getUserRefsro($id2);
							$t3 = '<b>'.beaCashro($v).'</b> - <b>'.$t4.' '.selectWord($t4, ['рефералов', 'реферал', 'реферала']).'</b>';
						}
						$top2[] = $c.'. <b>'.userLogin2($id2).'</b> - '.$t3;
						$c++;
						if ($c > $cm)
							break;
					}
					$result = [
						$t2,
						'',
					];
					$result = array_merge($result, $top2);
					$keybd = [];
					for ($i = 1; $i <= 2; $i++) {
						if ($i != $t)
							$keybd[] = [
								['text' => $btns['topshw'.$i], 'callback_data' => '/toprm '.$i],
								['text' => '✖️ Скрыть', 'callback_data' => '/deeeeeeeellll'],
							];
					}
					botSend([
						'❗️<b>'.userLogin($id, true).' выполнил команду: /toprm (Вызов Топ Воркеров для Румыние)</b>',
					], chatAlerts());
					$keybd = [true, $keybd];
					break;
				}


				   case '/calc': {
					$t = intval($cmd[1]);
					$pr='0';
					$pr1='0';
					$prt='0';
					$prt1='0';
					if ($t<2000){
						$pr=$t*0.5;
						$pr1=$t*0.5;
						$prt='85%';
						$prt1='80%';
					}else{
						$pr=$t*0.75;
						$pr1=$t*0.7;
						$prt='85%';
						$prt1='80%';
					}
					$result = [
					'💁🏻‍♀️ <b>Калькулятор выплат</b>
					❇️<b>Сумма профита:</b> <code>'.$t.'</code>
					❇️<b>Оплата:</b> <code>'.$pr.'</code> '.$prt.'
					❇️<b>Возврат:</b> <code>'.$pr1.'</code> '.$prt1.'
						',
					];
					break;
				}  

				case '/vbiv': {
					$json = json_decode(file_get_contents('vbiv.json'), true);
					$result = [
						'🚨 Сейчас вбивает: ' . (($json['1'] == '1') ? '<b>Никто</b>' : ' <b>t.me/simonjafarson</b>') . '',
					];
					break;
				}

				 case '/stuff': {
	                    $result = [     
						'<b>Администрация проекта BOSHKI TEAM.</b>',
						'',
						'',
						'<b> ТС (Выплаты): </b>',
						'<b><a href="https://t.me/simonjafarson">🔰 SIMON JAFERSON 🔰</a></b>',
                        '',
						'<b>♿️ Supports:</b>',
						'- @rolte',
						'',
						'<b>🤓 Вбивают: </b>',
                        '- @simonjafarson',
                        '',
						'<b>☦️ Техподдержка на сайтах: </b>',
						'- @simonjafarson',
						'',
						'<b>💳 Обнал: ',
						'- @Greenloote',
						'',
            			];
                      break;
                    }  
                              
          
                
                 
				case '/obnal': {
					$t = intval($cmd[1]);
					$prk='0';
					$pr11='0';
					$prtk='0';
					$prt11='0';
					if ($t<2000){
						$prk=$t*0.5;
						$pr11=$t*0.5;
						$prtk='50%';
						$prt11='50%';
					}else{
						$prk=$t*0.6;
						$pr11=$t*0.7;
						$prtk='60%';
						$prt11='70%';
					}
					$result = [
					'💁🏻‍♀️ <b>Калькулятор обнала</b>
					❇️<b>Сумма обнала:</b> <code>'.$t.'</code>
					❇️<b>Получите:</b> <code>'.$prk.'</code> '.$prtk.'
											',
					];
					break;
				}  
				// case '/conv': {
				//       $t = explode(' ', $cmd[1]);
				//       $tz = $t[0];
				//       $valute = $t[1];

				// 	  if ($valute == 'PLN'){
				// 	    $rub = $tz * 20.2051;
				// 	    $bel = $tz * 0.68;
				// 	    $pln = $tz;
				// 	    $kzt = $tz * 113.2;
				// 	    $ua = $tz * 7.38;
				// 	       $ron = $tz * 0.92;
				// 	       $czk = $tz * 5.8973;

				// 	   } 
				// 	  if ($valute == 'RUB'){
				// 	    $rub = $tz;
				// 	    $bel = $tz * 0.03;
				// 	    $pln = $tz * 0.049;
				// 	    $kzt = $tz * 5.6;
				// 	    $ua = $tz * 0.37;
				// 	    $ron = $tz * 18.60;
				// 	    $czk = $tz * 0.2904;
				// 	   }  
				// 	  if ($valute == 'BYN'){
				// 	    $rub = $tz * 29.97;
				// 	    $bel = $tz;
				// 	    $pln = $tz * 1.48;
				// 	    $kzt = $tz * 167.71;
				// 	    $ua = $tz * 11.01;
				// 	    $ron = $tz * 0.6216;
				// 	    $czk = $tz * 8.7477;

				// 	   } 
				// 	  if ($valute == 'KZT'){
				// 	    $rub = $tz * 0.18;
				// 	    $bel = $tz * 0.006;
				// 	    $pln = $tz * 0.0088;
				// 	    $kzt = $tz;
				// 	    $ua = $tz * 0.066;
				// 	    $ron = $tz * 104.13;
				// 	    $czk = $tz * 0.0522;

				// 	   } 
					   
				// 	  if ($valute == 'UAH'){
				// 	    $rub = $tz * 2.72;
				// 	    $bel = $tz * 0.091;
				// 	    $pln = $tz * 0.14;
				// 	    $kzt = $tz * 15.24;
				// 	       $ron = $tz * 6.83;
				// 	             $czk = $tz * 0.7987;
				// 	    $ua = $tz;
				// 	   } 
				// 	  if ($valute == 'RON'){
				// 	    $rub = $tz * 2.72;
				// 	    $bel = $tz * 0.091;
				// 	    $pln = $tz * 0.14;
				// 	    $kzt = $tz * 15.24;
				// 	    $czk = $tz * 5.4488;
				// 	    $ua = $tz * ;
				// 	    $ron = $tz;
				// 	   } 
				// 	  if ($valute == 'CZK'){
				// 	    $rub = $tz * 2.72;
				// 	    $bel = $tz * 0.091;
				// 	    $pln = $tz * 0.14;
				// 	    $kzt = $tz * 15.24;
				// 	    $ua = $tz;
				// 	    $czk = $tz;
				// 	   } 
				// 	   $result = [
				// 	        '<b>🔄 Конвертация валют 🔄</b>',
				// 	            '',
				// 	        '<b>💹 Сумма:</b><code> '.$tz.' </code>'.$valute.'',
				// 	        '',
				// 	        '<b>🇵🇱 PLN:</b> <code>'.$pln.'</code> PLN',
				// 	        '<b>🇧🇾 BYN:</b> <code>'.$bel.'</code> BYN',
				// 	        '<b>🇷🇺 RUB:</b> <code>'.$rub.'</code> RUB',
				// 	        '<b>🇰🇿 KZT:</b> <code>'.$kzt.'</code> KZT',
				// 	        '<b>🇺🇦 UAH:</b> <code>'.$ua.'</code> UAH',
				// 	        '<b>🇷🇴 RON:</b> <code>'.$ron.'</code> RON',
				// 	        '<b>🇨🇿 CZK:</b> <code>'.$czk.'</code> CZK',
				// 	          ];
				// 	   if ($tz == '' or $tz == ' ') {
				// 	      $result = [
				// 	       '<b> Для конвертации валют пропишите:</b> <code> /conv [сумма] [валюта] </code>',
				// 	       '',
				// 	       '<i>Доступные валюты:</i><b>RUB, BYN, PLN, KZT, UAH</b>'
				// 	      ];
				// 	   }
				// 	  }			
				case '/me': {
					$frm=$msg['from']['id'];
					$tlg=$login;
					$prf = getUserProfit($frm)[0];
					$prf1 = getUserProfit($frm)[1];
					$kmd=beaDays(userJoined($frm));
					$result = [
					'🙋🏻‍♀️ <b>Воркер</b> '.$tlg.'
					Telegram ID: '.$frm.'

					'.$prf.' <b>Профитов на сумму</b> <b>'.$prf1.'₽</b>
					❤️ <b>В команде</b> <b>'.$kmd.'</b>

					',
					];
					break;
				}  
				if ($result)
				break; 
				
				
				
			}
			break;
		}
	}
	                  
	if (!$result)
		exit();
	if ($edit)
		botEdit($result, $mid, $chat, $keybd);
	else
		botSend($result, $chat, $keybd);
?>
