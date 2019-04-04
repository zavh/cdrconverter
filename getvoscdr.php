<?php
chdir(dirname(__FILE__));
$numcols=0;
$confh = fopen("conf.getvoscdr", "r"); //Configuration Handler
$conf = array();
while(! feof($confh)){
	$line = trim(fgets($confh));
	$temp = explode(":",$line);
	if(count($temp) > 1 ){
		$conf[$temp[0]] = $temp[1];
	}
}
//print_r($conf);
fclose($confh);
if ($handle = opendir($conf['cdr-source'])) {
    print "Directory handle: $handle\n";
    print "Entries:\n";

	$filearray = array();
	$counter = 0;


	while (false !== ($entry = readdir($handle))) {
                $pattern = "/^cdr_\S*.csv$/";
                if(preg_match($pattern, $entry)){
                        $filearray[$counter]=$entry;
			$counter++;
			print $entry."\n";
                }
        }
}
// Reading columns
$filecol = fopen("cdr.column", 'r');
while (($line = fgetcsv($filecol)) !== FALSE) {
  $columnnames[$numcols]=$line;
  $numcols=$numcols+1;
}
fclose($filecol);
//Reading CDR data

$cdrdata = array();
$numlines = 0;

for ($k=0;$k<count($filearray);$k++){
	$file = fopen($filearray[$k], 'r');
	while (($line = fgetcsv($file)) !== FALSE) {
		$linecdr[$numlines]=$line;
  		if($linecdr[$numlines][23]==0) continue;
  		for($i=0;$i<$numcols;$i++){
	  		$cdrdata[$numlines][$columnnames[$i][0]] = $linecdr[$numlines][$i];
  		}
		dbEntrySqlProcess($cdrdata[$numlines]);
		$numlines++;
	}
	fclose($file);

	unlink($filearray[$k]);

}
closedir($handle);
print "Number of CDR files: $counter\n";
print "Number of records:". count($cdrdata)."\n";

function dbEntrySqlProcess($successCdr){
	require '/home/billing/voscdr/cdrconverter/db_read.php';
	$successCdr['callertogatewaye164'] = leadingPlusRemover($successCdr['callertogatewaye164']);
	$successCdr['calleetogatewaye164'] = leadingPlusRemover($successCdr['calleetogatewaye164']);
	$successCdr['calleeaccesse164'] = leadingPlusRemover($successCdr['calleeaccesse164']);
	$successCdr['calleraccesse164'] = leadingPlusRemover($successCdr['calleraccesse164']);
	$successCdr['calleee164'] = leadingPlusRemover($successCdr['calleee164']);
	$successCdr['calleee164'] = leadingPlusRemover($successCdr['calleee164']);

	$sql  = "INSERT INTO `webcdr` ";
	$sql .= "(";
	$sql .= "`csn`, `ans_time`, `end_time`, `conversation_time`, `caller_number`, `called_number`, ";
	$sql .= "`trunk_group_in`, `trunk_group_out`, `fractionbit3`, `connected_number`, `dial_number`, ";
	$sql .= "`caller_number_before_change`, `called_number_before_change`, `caller_seize_duration`, ";
	$sql .= "`called_seize_duration`, `Incoming Route ID`, `Outgoing Route ID`, ";
	$sql .= "`alerting time`, `caller physical number`, `callee physical number`, `Caller_call_id`, `Called_call_id`, `processed`";
	$sql .= ")";
	$sql .= "VALUES (NULL, ";
	$sql .= "'".$successCdr['starttime']."',"; //ans_time
	$sql .= "'".$successCdr['stoptime']."',";  //end_time
	$sql .= "'".$successCdr['holdtime']."',";  //conversation_time
	$sql .= "'".$successCdr['callertogatewaye164']."',"; //Remove leading +, change alphanumeric to default caller_number
	$sql .= "'".$successCdr['calleetogatewaye164']."',"; //Remove leading +, change alphanumeric to default called_number
	$sql .= "'".sizeLimitter($successCdr['callergatewayid'],16)."',"; //trunk_group_in
	$sql .= "'".sizeLimitter($successCdr['calleegatewayid'],16)."',"; //trunk_group_out
	if($successCdr['endreason'] == '-8') //fractionbit3 
		$sql .= "'16',";
	else if($successCdr['endreason'] == '-7')
		$sql .= "'17',";
	else $sql .= "'21',";
	$sql .= "'".$successCdr['calleetogatewaye164']."',"; //connected_number
	$sql .= "'".$successCdr['calleeaccesse164']."',"; //dial_number
	$sql .= "'".$successCdr['calleraccesse164']."',"; //caller_number_before_change
	$sql .= "'".$successCdr['calleee164']."',"; //called_number_before_change
	$sql .= "'".$successCdr['callerpdd']."',"; //caller_seize_duration
	$sql .= "'".$successCdr['calleepdd']."',"; //called_seize_duration
	$sql .= "'".sizeLimitter($successCdr['customername'],16)."',"; //Incoming Route ID
	$sql .= "'".sizeLimitter($successCdr['agentname'],16)."',"; //Outgoing Route ID
	$sql .= "'".($successCdr['starttime']-4)."',"; //alerting time
	$sql .= "'".$successCdr['callertogatewaye164']."',"; //caller physical number
	$sql .= "'".$successCdr['calleetogatewaye164']."',"; //callee physical number
	$sql .= "'".sizeLimitter($successCdr['callercallid'],64)."',"; //Caller_call_id
	$sql .= "'".sizeLimitter($successCdr['calleecallid'],64)."',"; //Called_call_id
	$sql .= "'0'"; //processed
	$sql .= ")";
	print "$sql\n";
	$result = $conn->query($sql);
	require '/home/billing/voscdr/cdrconverter/db_close.php';
}

function leadingPlusRemover($phNumber){
	$phNumArr = str_split($phNumber);
	if($phNumArr[0] == '+')
		$phNumber =  substr($phNumber, 1);
	if (!ctype_digit($phNumber)) $phNumber = '9992129541';
	return $phNumber;
}

function sizeLimitter($val, $intended){
	if(strlen($val)>$intended) return substr($val, 0, $intended);
	else return $val;
}
?>
