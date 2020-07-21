<?php

Class ReadSMS{
    public $access_token = 'o.IyDRBeGNdaskJDpGFIUiBv3ZSsLdBIUJ';
    
    public function CreateHeaders ($array) {
		$headers = array();
		foreach ($array as $key => $value) {
			$headers[] = $key.": ".$value;
		}
		return $headers;
    }
    
    public function request ($method, $url, $headers = array(), $data = null) {
		$handle = curl_init();
		if (!is_null($data)) {
			curl_setopt($handle, CURLOPT_POSTFIELDS, is_array($data) ? json_encode($data) : $data);
			if (is_array($data)) $headers = array_merge(array("Content-Type" => "application/json"), $headers);
		}
		curl_setopt_array($handle, array(
			CURLOPT_URL => $url,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_PROXY => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTPHEADER => $this->CreateHeaders($headers)
		));
		$response = curl_exec($handle);
		$http_code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		if ($result = json_decode($response, true)) {
			return $result;
		}
		return $response;
    }

    public function GetStringBetween($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    public function __construct ($access_token = null){
        if(is_null($access_token)) return false;

        $this->access_token = $access_token;
    }

    public function DevicesList(){
        if (is_null($this->access_token)) return false;
        $result = $this->request("GET", "https://api.pushbullet.com/v2/devices", [
            "Access-Token" => $this->access_token
        ]);
        return $result;
    }

    public function GetTrueWalletOTP($iden = null){
        if(is_null($iden)) return false;
        $result = $this->request("GET", "https://api.pushbullet.com/v2/permanents/".$iden."_threads", [
            "Access-Token" => $this->access_token
        ]);
        foreach($result as $title){
            foreach($title as $data){
                if($data["recipients"][0]["name"] == "TrueMoney"){
                    $ref = $this->GetStringBetween($data["latest"]["body"], "(Ref: ", ")");
                    $otp_code = $this->GetStringBetween($data["latest"]["body"], "รหัส OTP คือ ", " (");
                    return [
                        "ref" => $ref,
                        "otp_code" => $otp_code
                    ];
                }
            }
        }
        return false;
    }
}
