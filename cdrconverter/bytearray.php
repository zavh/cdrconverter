<?php
require_once("xml.php");
$filename = "test.dat";
$handle = fopen($filename, "rb"); 
$fsize = filesize($filename); 
echo "File Size:$fsize <br/>";
$num_records = $fsize/907;
echo "Number of records: $num_records<br/>";
$contents = fread($handle, 907); 
$byteArray = unpack("C*",$contents); 
echo "<table><tr><td>Byte</td><td>Decimal</td><td>binary</td>";
for ($k=1;$k<908;$k++){
//echo "<tr><td>bytearray[$k]</td><td>".$byteArray[$k]."</td><td>".sprintf( "%08d",decbin($byteArray[$k]))."</td></tr>";


}
echo "</table>";
$huawei = $cdrdef->children;
$numelements = count($huawei);
echo "Number of Elements:$numelements<br>";
$fields = array();
$fractionFields = array();
for($i=0;$i<$numelements;$i++){

	$fractionbit = $huawei[$i]->attributes["fractionbit"];
	$separatedchunks = $huawei[$i]->attributes["separatedchunks"];
	$defexists = $huawei[$i]->attributes["definition"];
	$reversed = $huawei[$i]->attributes["reversed"];
	$isDate = $huawei[$i]->attributes["date"];
                $name = $huawei[$i]->attributes["name"];
                $length = $huawei[$i]->attributes["length"];
                $offset = $huawei[$i]->attributes["offset"];

	// Handle fields which has continuous byte(s)
	if($fractionbit==0 && $separatedchunks==0 && $isDate==0) {
		$fields[$name] = transFields($byteArray, $offset, $length, $reversed);
		if($huawei[$i]->attributes["bcd"]==1)$fields[$name] = processBCD($fields[$name]);
	}

	if($isDate==1) $fields[$name] = formattedDate(individualBytes($byteArray, $offset, $length));

	if($fractionbit==1){
		$fractionFields[$name] = transFields($byteArray, $offset, $length, 0); 
		$individualbits = $huawei[$i]->children;
		$numFractions = count($individualbits);
		for($m=0;$m<$numFractions;$m++){
			$fractionName = $individualbits[$m]->attributes['name'];
			$numBits = $individualbits[$m]->attributes['bit'];
			$position = $individualbits[$m]->attributes['position'];
			$fractionDef = $individualbits[$m]->attributes['definition'];
			$fields[$fractionName] = substr ( $fractionFields[$name], $position, $numBits );

			if($fractionDef == 1){ //print_r($individualbits);
				$definition = $individualbits[$m]->children;
				$defcount = count($definition);
				for($j=0;$j<$defcount;$j++){
                        		if($definition[$j]->attributes['value']==bindec($fields[$fractionName]))
                                		{$fields[$fractionName] = $definition[$j]->content;
                                		}
                		}
			}
		}
	}
        // Handle field value interpretations. A field can have fixed values.
        // Depensing on the value coming from CDR field, the interpretation is
        // derived from CDR definition
	if($defexists == 1){
		$definition = $huawei[$i]->children;
		$defcount = count($definition);
                for($j=0;$j<$defcount;$j++){
			if($definition[$j]->attributes['value']==bindec($fields[$name]))
				{$fields[$name] = $definition[$j]->content;
                                }
                }
        }
}
print_r($fields);
echo "csn:".bindec($fields['csn'])."<br>";
echo "length:".bindec($fields['length'])."<br>";
echo "net_type:".$fields['net_type']."<br>";
echo "bill_type:".$fields['bill_type']."<br>";
echo "checksum:".bindec($fields['check_sum'])."<br>";
echo "ans_time:".$fields['ans_time']."<br>";
echo "end_time:".$fields['end_time']."<br>";
echo "conversation_time:".bindec($fields['conversation_time'])."<br>";
echo "caller_number:".$fields['caller_number']."<br>";
echo "called_number:".$fields['called_number']."<br>";
echo "trunk_group_in".$fields['trunk_group_in']."<br>";
fclose($handle);
//print_r($fields);
function transFields($byteArray, $offset, $byteSize, $reversed){
	//$fields = array();
	if($reversed==1){
	for ($i=$byteSize;$i>0;$i--){
		$position = $offset+$i;
		$fields .=sprintf( "%08d",decbin($byteArray[$position]));
	}}
	else {
        for ($i=1;$i<$byteSize+1;$i++){
                $position = $offset+$i;
                $fields .=sprintf( "%08d",decbin($byteArray[$position]));
        }
	}

return $fields;
}

function individualBytes($byteArray, $offset, $byteSize){

        for ($i=1;$i<($byteSize+1);$i++){ 
                $position = $offset+$i;
                $fields[$i] = $byteArray[$position];
        }
	return $fields;

}

function formattedDate($dateArray){

for ($i=1;$i<7;$i++){
	if($dateArray[$i]<10) $dateArray[$i] = "0".$dateArray[$i];
}
	$myyear = date("Y");
	$millenium = round($myyear,-2);
	$dateArray[1] = $dateArray[1] + $millenium;
	$formattedTime = $dateArray[1]."-".$dateArray[2]."-".$dateArray[3]." ".$dateArray[4].":".$dateArray[5].":".$dateArray[6];
	return $formattedTime;
}

function processBCD($BCDstream){
	$streamLength = strlen($BCDstream);
	$numBytes = $streamLength/8;
	$decodedBCD = array();
	$resultBCD = "";
	$BCDpos = 0;
	for($n=0;$n<$numBytes;$n++){
		$currentPos = $n*8;
		$oneByte = substr($BCDstream,$currentPos,8); 
		$temp = substr($oneByte,0,4); 
		if(bindec($temp)>9) continue;
		else{
			$decodedBCD[$BCDpos]=bindec($temp);
			$temp = substr($oneByte,4,4); if(bindec($temp)>9) continue;
			$decodedBCD[$BCDpos+1]=bindec($temp);
			$BCDpos= $BCDpos+2;
		}
	}

	$countBCD = count($decodedBCD);
	for ($i=0;$i<$countBCD;$i++){
		$resultBCD .= $decodedBCD[$i];
	}
	return $resultBCD;
}
////print_r($byteArray); 
?>
