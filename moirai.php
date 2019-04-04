<?php
chdir(dirname(__FILE__));
$logfile = "controller.log";
date_default_timezone_set('Asia/Dhaka');
$clog = fopen($logfile, "a");
$runornot = processCheck();
if($runornot){
	$log = "[moirai]|No process running. getvoscdr process will be spawned\n";
	writeLogs($clog, $log);
	shell_exec('php getvoscdr.php');
	$log = "[moirai]|Exiting\n";
	writeLogs($clog, $log);

}
else{ 
	$log =  "[moirai]|Process already running. Spawn stopped\n";
	writeLogs($clog, $log);
}
fclose($clog);


function processCheck(){
	$output = shell_exec('ps -ef |grep getvoscdr.php');
	$oarray = explode("\n", $output);
	$cleanvalue = array();
	$pdef = array('UID','PID','PPID','C', 'STIME', 'TTY', 'TIME', 'CMD', 'Program');
	$cleancount = 0;
	for($i=0;$i<count($oarray);$i++){
		if(!strpos($oarray[$i],"php getvoscdr.php")) continue;
		$temp = explode(" ", trim($oarray[$i]));
		$rc = 0; //real count
		for($k=0;$k<count($temp);$k++){
			if($temp[$k] != null){
				$cleanvalue[$cleancount][$pdef[$rc]] = $temp[$k];
				$rc++;
			}
		}
		$cleancount++;
	}
	$pcount = 0;
	for($i=0;$i<count($cleanvalue);$i++){
		if($cleanvalue[$i]['CMD'] == "php" && $cleanvalue[$i]['Program'] =="getvoscdr.php")
			$pcount++;
	}
	if($pcount > 0) {
		print_r($cleanvalue);
		return FALSE;
	}
	return TRUE;

}

function writeLogs($fh, $log){
        $logdate = "[".date("Y-m-d H:i:s")."]";
        fwrite($fh,$logdate."|".$log);
}


function archiveLog($logfile){
	if(!file_exists($logfile)) return;

}
