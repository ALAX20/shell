    <?php
	function xReq($phone, $message) {
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.sms.to/sms/send",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS =>"{\n    \"message\": \".$message.\",\n    \"to\": \".$phone.\",\n    \"sender_id\": \"SMSto\"\n}",
		  CURLOPT_HTTPHEADER => array(
			  "Content-Type: application/json",
			  "Accept: application/json",
			  "Authorization: Bearer АПИ ТОКЕН"
			),
		));
		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
	}
	function GetBalance() {
		 $curl = curl_init();
		 curl_setopt_array($curl, array(
           CURLOPT_URL => "https://auth.sms.to/api/balance?api_key=АПИ ТОКЕН",
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_ENCODING => "",
           CURLOPT_MAXREDIRS => 10,
           CURLOPT_TIMEOUT => 0,
           CURLOPT_FOLLOWLOCATION => true,
           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
           CURLOPT_CUSTOMREQUEST => "GET",
         ));
		$response = curl_exec($curl);
		curl_close($curl);
		return $response['balance'];
	}
?>