<?php

namespace App\Api;

use GuzzleHttp\Client;
use DateTime;


/*
	[e等APP]教資整合API
*/
class ELevelApp{
	const BRANCHCODE = [
		1 => null,
		2 => 5401
	];

	const APIURL = "https://appweb-fet.hrd.gov.tw";
	/*
		一、 ［接收］研習班期資料 
	*/
	public function syncClass($data, $branch)
	{
		$url = ELevelApp::APIURL.'/api/sunnet_personnel.php';
		$code = $this->computeCheckCode($branch);
		$data = [

		];

		$client = new Client();
		$client->setDefaultOption('verify', false);

		$result = $client->post($url, [
		    'form_params' => compact(['code', 'data'])
		]);		
		dd($code);
		// $this->checkCode[]; 
	}

	/*
		七、［回傳］簽到資料 
	*/
	public function getSign($class, $term, $branch)
	{
		$url = ELevelApp::APIURL.'/api/sunnet_sign.php';
		$code = $this->computeCheckCode($branch);
		
		//	測試
		$class = '109864';
		$term = '01';
		// 測試

		$client = new Client();

		$result = $client->request('POST', $url, [
		    'headers' => [
		        'User-Agent' => 'Guzzle/6.5',        
		    ],			
			'verify' => false,
		    'form_params' => compact(['code', 'class', 'term'])
		]);	

		$response = json_decode($result->getBody());
		dd($response);
		dd(json_encode(compact(['code', 'class', 'term'])));
		die;
	}

	// 產生驗證代碼
	public function computeCheckCode($branch)
	{
		// 驗證代碼產生規則：（當天日期轉數字 8 碼 20200518 + 指定代碼 5401）* 365 
		$today = new DateTime();
		$code = ((int)$today->format('Ymd') + ELevelApp::BRANCHCODE[$branch]) * 365;
		return $code;
	}
}