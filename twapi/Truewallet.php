<?php

/**
* TrueWallet Class
 *
 * @category  Payment Gateway
 * @package   php-truewallet-class
 * @author    Likecyber <cyber2friends@gmail.com>
 * @copyright Copyright (c) 2018-2019
 * @license   https://creativecommons.org/licenses/by/4.0/ Attribution 4.0 International (CC BY 4.0)
 * @link      https://github.com/likecyber/php-truewallet-class
 * @version   2.1.0
**/

class TrueWalletClass {
	public $credentials = array();
	public $access_token = null;
	public $reference_token = null;
	public $curl_options = null;
	public $data = null;
	public $response = null;
	public $http_code = null;
	public $mobile_api_gateway = "https://mobile-api-gateway.truemoney.com/mobile-api-gateway/";
	public $secret_key = "9LXAVCxcITaABNK48pAVgc4muuTNJ4enIKS5YzKyGZ";
	public $device_id = "";
	public $mobile_tracking = ""; // base64 $device_id
	
	public function generate_identity () {
		$this->mobile_tracking = base64_encode(openssl_random_pseudo_bytes(40));
		$this->device_id = substr(md5($this->mobile_tracking), 16);
		return implode("|", array($this->device_id, $this->mobile_tracking));
	}

	public function __construct ($username = null, $password = null, $reference_token = null) {
		if (empty($this->device_id) || empty($this->mobile_tracking)) {
			$identity_file = dirname(__FILE__)."/".basename(__FILE__, ".php").".identity";
			if (file_exists($identity_file)) {
				list($this->device_id, $this->mobile_tracking) = explode("|", file_get_contents($identity_file));
			} else {
				file_put_contents($identity_file, $this->generate_identity());
			}
		}
		if (!is_null($username) && !is_null($password)) {
			$this->setCredentials($username, $password, $reference_token);
		} elseif (!is_null($username)) {
			$this->setAccessToken($username);
		}
	}
	public function setCredentials ($username, $password, $reference_token = null, $type = null) {
		if (is_null($type)) $type = filter_var($username, FILTER_VALIDATE_EMAIL) ? "email" : "mobile";
		$this->credentials["username"] = strval($username);
		$this->credentials["password"] = strval($password);
		$this->credentials["type"] = strval($type);
		$this->setAccessToken(null);
		$this->setReferenceToken($reference_token);
	}
	public function setAccessToken ($access_token) {
		$this->access_token = is_null($access_token) ? null : strval($access_token);
	}
	public function setReferenceToken ($reference_token) {
		$this->reference_token = is_null($reference_token) ? null : strval($reference_token);
	}
	public function request ($method, $endpoint, $headers = array(), $data = null) {
		$this->data = null;
		$handle = curl_init();
		if (!is_null($data)) {
			curl_setopt($handle, CURLOPT_POSTFIELDS, is_array($data) ? json_encode($data) : $data);
			if (is_array($data)) $headers = array_merge(array("Content-Type" => "application/json"), $headers);
		}
		curl_setopt_array($handle, array(
			CURLOPT_URL => $this->mobile_api_gateway.ltrim($endpoint, "/"),
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_PROXY => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_USERAGENT => "V1/5.5.1 (com.tdcm.truemoneywallet; build:674; iOS 13.3.1) Alamofire/4.8.2",
			CURLOPT_HTTPHEADER => $this->buildHeaders($headers)
		));
		if (is_array($this->curl_options)) curl_setopt_array($handle, $this->curl_options);
		$this->response = curl_exec($handle);
		$this->http_code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		if ($result = json_decode($this->response, true)) {
			if (isset($result["data"])) $this->data = $result["data"];
			return $result;
		}
		return $this->response;
	}
	public function buildHeaders ($array) {
		$headers = array();
		foreach ($array as $key => $value) {
			$headers[] = $key.": ".$value;
		}
		return $headers;
	}
	public function getTimestamp() {
		return round(microtime(true) * 1000);
	}
    	public function hashPassword($username, $password, $time) {
        	$a = hash('sha256', $username . $password);
        	$b = hash('sha256', (strlen($time) > 4) ? substr($time, 4) : $time);
        	return hash('sha256', $b . $a);
    	}
	public function RequestLoginOTP () {
		if (!isset($this->credentials["username"]) || !isset($this->credentials["password"]) || !isset($this->credentials["type"])) return false;
		$timestamp = $this->getTimestamp();
		$result = $this->request("GET", "mobile-auth-service/v1/password/login/otp", array(
			"timestamp" => $timestamp,
			"type" => $this->credentials["type"],
			"username" => $this->credentials["username"],
			"password" => $this->hashPassword($this->credentials["username"], $this->credentials["password"], $timestamp)
		));
		return $result;
	}
	public function SubmitLoginOTP ($otp_code, $mobile_number = null, $otp_reference = null) {
		if (is_null($mobile_number) && isset($this->data["mobile_number"])) $mobile_number = $this->data["mobile_number"];
		if (is_null($otp_reference) && isset($this->data["otp_reference"])) $otp_reference = $this->data["otp_reference"];
		if (is_null($mobile_number) || is_null($otp_reference)) return false;
		$timestamp = $this->getTimestamp();
		$result = $this->request("POST", "/mobile-auth-service/v1/password/login/otp", array(
			"timestamp" => $timestamp,
			"X-Device" => $this->device_id
		), array(
			"brand" => "apple",
			"device_os" => "ios",
			"device_name" => "chick4nnn’s iPhone",
			"device_id" => $this->device_id,
			"model_number" => "iPhone 11 Pro",
			"model_identifier" => "iPhone 11 Pro",
			"app_version" => "5.5.1",
			"type" => $this->credentials["type"],
			"username" => $this->credentials["username"],
			"password" => $this->hashPassword($this->credentials["username"], $this->credentials["password"], $timestamp),
			"mobile_tracking" => $this->mobile_tracking,
			"otp_code" => $otp_code,
			"otp_reference" => $otp_reference,
			"timestamp" => $timestamp,
			"mobile_number" => $mobile_number
		));
		if (isset($result["data"]["access_token"])) $this->setAccessToken($result["data"]["access_token"]);
		if (isset($result["data"]["reference_token"])) $this->setReferenceToken($result["data"]["reference_token"]);
		return $result;
	}

	public function Logout () {
		if (is_null($this->access_token)) return false;
		return $this->request("POST", "/api/v1/signout/".$this->access_token);
	}
	public function GetProfile () {
		if (is_null($this->access_token)) return false;
		return $this->request("GET", "/user-profile-composite/v1/users/", array(
			"Authorization" => $this->access_token
		));
	}
	public function GetBalance () {
		if (is_null($this->access_token)) return false;
		return $this->request("GET", "/user-profile-composite/v1/users/balance/", array(
			"Authorization" => $this->access_token
		));
	}
	public function GetTransaction ($limit = 50, $start_date = null, $end_date = null) {
		if (is_null($this->access_token)) return false;
		if (is_null($start_date) && is_null($end_date)) $start_date = date("Y-m-d", strtotime("-2 days") - date("Z") + 25200);
		if (is_null($end_date)) $end_date = date("Y-m-d", strtotime("+1 day") - date("Z") + 25200);
		if (is_null($start_date) || is_null($end_date)) return false;
		return $this->request("GET", "/user-profile-composite/v1/users/transactions/history/?".http_build_query(array(
			"start_date" => strval($start_date),
			"end_date" => strval($end_date),
			"limit" => intval($limit),
		)), array(
			"X-Device" => $this->device_id,
			"Authorization" => $this->access_token
		));
	}
	public function GetTransactionReport ($report_id) {
		if (is_null($this->access_token)) return false;
		return $this->request("GET", "/user-profile-composite/v1/users/transactions/history/detail/".strval($report_id), array(
			"Authorization" => $this->access_token
		));
	}
}
?>
