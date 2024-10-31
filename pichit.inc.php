<?php
class pichit {

	public function get_token($username, $password) {
		$url = 'pichit.me/api/token/create/?username=' . $username . '&password=' . $password;
		 $ch = curl_init();
		 curl_setopt($ch, CURLOPT_URL, $url);
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		 curl_setopt($ch, CURLOPT_HTTPGET, true);
		 curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		             'Accept: application/json', 
		             'Content-type: application/json'
		           ));
		 $response = curl_exec($ch);
		 $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		 curl_close($ch);
		 $response = json_decode($response);
		 return $response->token;
	}
	
	public function check_token ($token) {
		$url = 'pichit.me/api/token/check/?token='.$token;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPGET, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: application/json', 
            'Content-type: application/json'
        ));
		$response = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		$response = json_decode($response);
		if(@$response->error) {
			return false;
		}else {
			return true;
		}
	}
	
	public function get_json_response($url,$token) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPGET, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    'SIMPLETOKEN-AUTH: ' . $token,
	        'Content-type: application/json',
	        'Accept: application/json'
		));
		$response = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return $response;
	}
	
	public function get_array_of_json($jsonstring) {
		$return = json_decode($jsonstring);
		return $return;
	}
}
?>