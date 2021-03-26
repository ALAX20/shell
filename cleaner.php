<?php
	$timemax = 172800;
	$time = time();
	for ($i = 0; $i < 2; $i++) {
		$isnt = ($i == 0);
		foreach (glob(($isnt ? 'items' : 'tracks').'/*') as $t) {
			$item = explode('.', basename($t))[0];
			$itemd = getItemData($item, $isnt);
			if ($time - $itemd[4] > $timemax) {
				$id = $itemd[3];
				delUserItem($id, $item, $isnt);
				botSend([
					'❗️ Ваш'.($isnt ? 'е объявление' : ' трек номер').' <b>'.$item.'</b> удален'.($isnt ? 'о' : '').' автоматически из-за неактуальности',
				], $id);
				botSend([
					'🗑 '.($isnt ? 'Объявление' : 'Трек номер').' <b>'.$item.'</b> <b>'.userLogin($id, true, true).'</b> было удалено автоматически из-за неактуальности',
				], chatAlerts());
			}
		}
	}
?>