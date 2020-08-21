<?php 

if (! function_exists('createModifyLog')) {
	/*
		新增資料修改紀錄 [ t35tb_new ]
		$progid => 程式 ID 
		$actionType => 異動動作 I => 新增, U => 更新, S => 查詢, R => 報表
		$logTable => 異動表,
		$beforeData => 異動前資料,
		$afterData => 異動後資料, 
		$sql => SQL 語法
	*/
	function createModifyLog($actionType, $logTable, $beforeData = null, $afterData = null, $sql = null)
	{
		if ($logTable == 't01tb' && $actionType !='I'){
			unset($beforeData[0]->planmk);
		}
		
		$progid = getProgid();

		foreach (compact(['progid', 'actionType', 'logTable']) as $key => $value){
			if (empty($value)){
				die("異動紀錄 {$key} 不可為空");
			}
		}

		$userid = Auth::user()->userid;   // 操作者
		$clientIp = request()->ip();      // 操作者 IP
		$now = new DateTime();            // 當前時間

		$logdate = $now->format('Ymd');
		$logdate = (substr($logdate, 0, 4) - 1911).substr($logdate, 4);
		$logtime = $now->format('H:i:s');

		$beforeData = empty($beforeData) ? null : json_encode($beforeData);
		$afterData = empty($afterData) ? null : json_encode($afterData);
		$sql = empty($sql) ? null : json_encode($sql);

		$modifyLog = [
			'logdate' => $logdate,
			'logtime' => $logtime,
			'userid' => $userid,
			'progid' => $progid,
			'source_ip' => $clientIp,
			'type' => $actionType,
			'logtable' => $logTable,
			'content_before' => $beforeData,
			'content_after' => $afterData,
			'sql' => $sql
		];

		return \App\Models\T35tbNew::create($modifyLog);
	}
}

/*
  檢查該功能是否需要紀錄
*/
if (! function_exists('checkNeedModifyLog')) {
	function checkNeedModifyLog($progid = null)
	{
		global $needModifyLog;

		if ($needModifyLog === null){
			$progid = ($progid === null) ? getProgid() : $progid;
			$m11tb = \App\Models\M11tb::where('progid', '=', $progid)->where('logmk', '=', 'Y')->first();
			$needModifyLog = !empty($m11tb);
		}

		return $needModifyLog;
	}
}

/*
	設定當前程式id
*/
if (! function_exists('setProgid')) {
	function setProgid($newProgid)
	{
		global $progid;
		$progid = $newProgid;
	}
}
/*
	取得當前程式id
*/
if (! function_exists('getProgid')) {
	function getProgid()
	{
		global $progid;
		return $progid;
	}
}
