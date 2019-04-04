<?php
date_default_timezone_set("Asia/Dhaka");
require_once("/home/billing/voscdr/cdrconverter/xml.php");
$huawei = $cdrdef->children;
$numelements = count($huawei);
$iteration = 0;
print "Number of Elements:$numelements\n";
require_once '/home/billing/voscdr/cdrconverter/constants.php';
require_once '/home/billing/voscdr/cdrconverter/db_read.php';
$bin_data = array();
$num_chunk = 0;
//Reading new records from DB
$sql = "SELECT * from webcdr where processed=0";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
	if(!isset($bin_data[$num_chunk])) //checking if chunk is ready
		$bin_data[$num_chunk] = ""; //make it ready if not
//$row["csn"] = $dbrow["csn"];//id
//$row["ans_time"] = $dbrow["ans_time"]; //starttime
//$row["end_time"] = $dbrow["end_time"]; //stoptime
	$tgin = $row["trunk_group_in"];
	$tgout = $row["trunk_group_out"];
	$row["conversation_time"] = $row["conversation_time"]*100;	//holdtime
	$row["caller_number"] = $row["caller_number"];//callertogatewaye164
	$row["called_number"] = $row["called_number"];//calleetogatewaye164
	$row["trunk_group_in"] = NameToDec($row["trunk_group_in"]); //callergatewayid |get summation of ascii to integer|
	$row["trunk_group_out"] = NameToDec($row["trunk_group_out"]); //calleegatewayid |get summation of ascii to integer|
	$row["fractionbit3"]            = "20"; //endreason |-8 maps to 20| -7 maps to 4|
//$row["connected_number"] = "8801716565025"; //calleetogatewaye164
//$row["dial_number"]	= "7564118801716565025"; //calleeaccesse164
	$row["Local_csn"] = "89963";// Auto counter generated in database
	$row["Incoming route identify number"] = $row["trunk_group_in"]; //trunk group and route number kept same
	$row["Outgoing route identify number"] = $row["trunk_group_out"]; //trunk group and route number kept same
//$row["caller_number_before_change"] = "654208159019"; //calleraccesse164
//$row["called_number_before_change"] = "118801716565025"; //calleee164
	$row["Ingress Media Gateway ID"] = $tgin;
	$row["Egress Media Gateway ID"] =  $tgout;
//$row["caller_seize_duration"] = "18119"; //callerpdd
//$row["called_seize_duration"] = "4617"; //calleepdd
	$row["Start Date and Time of Call Setup"] = $row["ans_time"] - 290;
	$row["call_setup_duration"] = ($row["ans_time"] - $row["Start Date and Time of Call Setup"])/10;
	$row["alerting time"] = $row["ans_time"];
	$row["caller physical number"] = $row["caller_number"];
	$row["callee physical number"] = $row["called_number"];
//$row["Caller_call_id"] = CallidToBin($row["Caller_call_id"]);
//$row["Called_call_id"] = CallidToBin($row["Called_call_id"]);
	$data = array(); //array t hold human readable data


	for ($i=0;$i<$numelements;$i++){
		if($huawei[$i]->attributes["value"] == "constant"){
			if($huawei[$i]->attributes["bcd"] == "1") 
				$data[$i]['val'] = makeBCD ($constant[$huawei[$i]->attributes["name"]], $huawei[$i]->attributes["length"]);
			else if($huawei[$i]->attributes["conversion"] == "string")
				$data[$i]['val'] = makeString($constant[$huawei[$i]->attributes["name"]], $huawei[$i]->attributes["length"]);
			else
				$data[$i]['val'] = $constant[$huawei[$i]->attributes["name"]];
		}
		if($huawei[$i]->attributes["value"] == "db"){
			if($huawei[$i]->attributes["date"] == "1")
				$data[$i]['val']=formattedDate(date("y-m-d-H-i-s",$row[$huawei[$i]->attributes["name"]]/1000));
			else if($huawei[$i]->attributes["bcd"] == "1")
				$data[$i]['val'] = makeBCD ($row[$huawei[$i]->attributes["name"]], $huawei[$i]->attributes["length"]);
			else if($huawei[$i]->attributes["conversion"] == "string")
				$data[$i]['val'] = makeString($row[$huawei[$i]->attributes["name"]], $huawei[$i]->attributes["length"]);
			else
				$data[$i]['val'] = $row[$huawei[$i]->attributes["name"]];
		}
		$data[$i]['format'] = $huawei[$i]->attributes["conversion"];
		$data[$i]['name'] = $huawei[$i]->attributes["name"];
	}
// writing binary stream
	for($i=0;$i<count($data);$i++){
		if($data[$i]['format'] == "sp" || $data[$i]['format'] == "bcd" || $data[$i]['format'] == "string") 
			$bin_data[$num_chunk] .= $data[$i]['val'];
		else $bin_data[$num_chunk] .= pack($data[$i]['format'], $data[$i]['val']);
	}
	$proc_done_sql = "UPDATE `webcdr` SET `processed` = '1' WHERE `webcdr`.`csn` = ".$row['csn'];
	$result1 = $conn->query($proc_done_sql);
	$iteration++;
	$num_chunk = (int)($iteration/1200); // when the number of records reach 
					   // multiples of 100, new cdr file will be created
					   // For new cdr, new array element of $bin_data will be used
}//binary data processing ends here

print "Writing binary data\n";
if($iteration>0){
	//	$fNameSql  = "SELECT MAX(csn) AS csn FROM webcdr";
	$fNameSql = "SELECT counter from cdrfilename where id=1";
	$result = $conn->query($fNameSql);
	$cdrNameTail = "";
	$cdrGlobalCount = 0;
	while($row = $result->fetch_assoc()) {
        	$cdrGlobalCount = $row['counter']+1;
	}
	//print_r($data);
	$num_chunks = count($bin_data);
	for($m=0;$m<$num_chunks;$m++){
		$cdrNameTail = sprintf('%08d',($cdrGlobalCount));
		$fp = fopen('/home/billing/voscdr/cdrconverter/convertedcdr/'.getFileName($cdrNameTail), 'wb'); // replace with your filename 
                     // (please also check file permission before write)
		fwrite($fp, $bin_data[$m]);
		fclose($fp);
		$cdrGlobalCount++;
	}
	$cdrGlobalCount = $cdrGlobalCount-1;
	$cdrGlobSQL = "UPDATE `cdrfilename` SET `counter` = '".$cdrGlobalCount."' WHERE `cdrfilename`.`id` = 1;";
	$result2 = $conn->query($cdrGlobSQL);
}
require_once '/home/billing/voscdr/cdrconverter/db_close.php';
#########################Main File Ends Here#############################

function formattedDate($unixTstamp){
	$dateArray = explode("-",$unixTstamp);
	$dateBinValue = "";
	for($i=0;$i<count($dateArray);$i++){
		$dateBinValue .= pack("C", $dateArray[$i]);;
	
	}
return $dateBinValue;
}

function getFileName($tail){
	$thisInstance = date("YmdHis");

	$fileName = "b".$thisInstance.$tail.".dat";
	return $fileName;
}

function makeBCD($target, $numBytes){
	$targetLength = strlen($target); $k = $targetLength;
	$bcdStreamLen = $numBytes*2;
	$bcdArray = array();
	$bcdBinary = "";
	for($i=0;$i<$bcdStreamLen;$i++){
		$bcdArray[$i] = "1111";
	}
	for($j = ($bcdStreamLen - 1); $j>($bcdStreamLen - 1 - $targetLength);$j = $j - 2){
		$bcdArray[$j - 1] = sprintf( "%04d",decbin(substr($target, ($targetLength - $k), 1)));
		if(($targetLength - $k + 1) == $targetLength) break;
		$bcdArray[$j] = sprintf( "%04d",decbin(substr($target, ($targetLength - $k + 1), 1)));
		$k = $k - 2;
	}

	for($i = ($numBytes*2 - 1);$i>0;$i = $i - 2){
		$tempByte = bindec($bcdArray[$i-1].$bcdArray[$i]);
		$bcdBinary .= pack("C", $tempByte);
	}
	return $bcdBinary;
}

function NameToDec($myname){
	include "/home/billing/voscdr/cdrconverter/db_read.php";
	$trunkid = 0;
//	$decPresentation = 0;
//	$nameLength = strlen($myname);

//	for($i=0;$i<$nameLength;$i++){
//		$decPresentation = $decPresentation + ord(substr($myname, $i, 1));
//	}
	//	return $decPresentation;
	$sql = "SELECT trunkid from trunkgroup where trunk_name='".$myname."'";
//	echo $sql;
	$result1 = $conn->query($sql);
	if($result1->num_rows > 0){
		while($row = $result1->fetch_assoc()){
			$trunkid = $row['trunkid'];
		}
	}
	else {
		$newtrunksql = "INSERT INTO `trunkgroup` (`trunkid`, `trunk_name`) VALUES (NULL, '".$myname."');";
		$result2 = $conn->query($newtrunksql);
		$trunkid = $conn->insert_id;
	}
	include "/home/billing/voscdr/cdrconverter/db_close.php";
	return $trunkid;
}

function IPtoDec($myIP){
	$iparray = explode(".",$myIP); 
	$ipbinary = "";
	$iparraycount = count($iparray);

	for($i=0;$i<$iparraycount;$i++){
		$ipbinary .= sprintf( "%08d",decbin($iparray[$i]));
	}
	return bindec($ipbinary);
}

function makeString($mystring, $byteSize){
	$filler = pack("C","0");;
	$stringToBin = "";
	$stringLength = strlen($mystring);

	for($i = 0 ; $i<$stringLength;$i++){
		$stringToBin .= pack("C",ord(substr($mystring,$i,1)));
	}
	for($j=0;$j<($byteSize - $stringLength - 1);$j++){
		$filler .= pack("C","0" );
	} 
	if($byteSize == $stringLength) return $stringToBin;
	else $stringToBin = $stringToBin.$filler;
	print "Printing StringToBin:".$stringToBin."\n";
	return $stringToBin;
}

function CallidToBin($callid){
	$callid = sprintf('%064d',$callid);
//	$callidlen = strlen($callid);
//	for($i=0;$i<(64-$callidlen);$i++){
//		$callid .= "0";
//	}
	print "Printing callid with filler:".$callid."\n";
	return $callid;
}
?>
