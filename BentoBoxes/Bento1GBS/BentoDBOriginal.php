<?php
// Standard inclusions
include("./pData.class");
include("./pChart.class");

ignore_user_abort(1); // run script in background
set_time_limit(0); // run script forever

##############################################################################################################################
#function: extract_attachments
#parameters: $connection, $message_number
#return: $attachments
#
# Function to extract the attachments from the email read
# returns array of attachments of each email
#
###############################################################################################################################
function extract_attachments($connection, $message_number) {
	$attachments = array();
	$structure = imap_fetchstructure($connection, $message_number);

	if(isset($structure->parts) && count($structure->parts)) {
			
		for($i = 0; $i < count($structure->parts); $i++) {

			$attachments[$i] = array(
					'is_attachment' => false,
					'filename' => '',
					'name' => '',
					'attachment' => ''
					);

					if($structure->parts[$i]->ifdparameters) {
						foreach($structure->parts[$i]->dparameters as $object) {
							if(strtolower($object->attribute) == 'filename') {
								$attachments[$i]['is_attachment'] = true;
								$attachments[$i]['filename'] = $object->value;
							}
						}
					}

					if($structure->parts[$i]->ifparameters) {
						foreach($structure->parts[$i]->parameters as $object) {
							if(strtolower($object->attribute) == 'name') {
								$attachments[$i]['is_attachment'] = true;
								$attachments[$i]['name'] = $object->value;
							}
						}
					}

					if($attachments[$i]['is_attachment']) {
						$attachments[$i]['attachment'] = imap_fetchbody($connection, $message_number, $i+1);
						if($structure->parts[$i]->encoding == 3) { // 3 = BASE64
							$attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
						}
						elseif($structure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
							$attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
						}
					}

		}
			
	}

	return $attachments;

}

##############################################################################################################################
#function: function_exportToCSV
#parameters:
#return:
#
# Function to export all the table contents to recordings.csv file and the last 24 rows to recordings24.csv
#
###############################################################################################################################
function function_exportToCSV($hostname, $db_user, $db_password, $db_table, $database) {

	//open files to write
	$file = "recordings.csv";
	$file2 = "recordings24.csv";

	$fp = fopen($file,'w') or die('cant open file');
	$fp2 = fopen($file2,'w') or die('cant open file');

	$db2 = mysql_connect($hostname, $db_user, $db_password) or die("could not open database - try again Later!!");//connecting to mysql
	mysql_select_db($database,$db2);

	// command to query all the contents of the data
	$result = mysql_query("SELECT * FROM  `BENTOBOX1` ORDER BY  `BENTOBOX1`.`RecordTime` ASC") or die(mysql_error());
	$count_result = mysql_query("SELECT count(*) AS 'NoOfRecordings' FROM $db_table") or die(mysql_error());
	$count_row = mysql_fetch_array($count_result);
	$count = 0;
	//$prev=array("2012-04-19 17:35",10.5,"Normal",77.4,47.38,82.61,30.46,82.4,70.28,81.28,72.42,72.52,73.76,40.27,45.94,66.54,66.94,72.82,34.45);
	//fetch the data row-wise and insert , between the fields and write to the file
	while($row = mysql_fetch_array($result)) {
		$string_row = $row[RecordTime].",".$row[BatteryPercent].",".$row[Rate].",".$row[Temperature1].",".$row[Temperature2].",".$row[Temperature3].",".$row[Temperature4].",".$row[Temperature5].",".$row[Temperature6].",".$row[Temperature7].",".$row[Temperature8].",".$row[Temperature9].",".$row[Temperature10].",".$row[Temperature11].",".$row[Temperature12].",".$row[Temperature13].",".$row[Temperature14].",".$row[Temperature15].",".$row[Temperature16]."\n";
		fwrite($fp,$string_row);

		// To write the last 24 lines to the file
		if(($count_row[NoOfRecordings]-$count)<=24){
			// If $prev is not intialized, assign the current row as previous row.
			// This condition is true only for the first row, so previous = row
			if(!$prev){
				$prev = $row;
				echo "in if block <br />";
			}
			$recordTime = split(" ",$row[RecordTime]);
			$recordTime[1] = substr($recordTime[1],0,5);
			for($column=3; $column<sizeof($row); $column++){
				if($row[$column]-$prev[$column]>=20 || $prev[$column]-$row[$column]>=20){
					echo "adjusted values: current value: ".$row[$column];
					$row[$column]="xxxxx";//$prev[$column];
					echo "New Value:".$row[$column]."<br />";
				}
				if($row[$column]<0){
					$row[$column]=0;
				}elseif($row[$column]>100){
					$row[$column]="";//100;
				}
			}
			$string_row = $recordTime[1].",".$row[BatteryPercent].",".$row[Rate].",".$row[3].",".$row[4].",".$row[5].",".$row[6].",".$row[7].",".$row[8].",".$row[9].",".$row[10].",".$row[11].",".$row[12].",".$row[13].",".$row[14].",".$row[15].",".$row[16].",".$row[17].",".$row[18]."\n";
			fwrite($fp2,$string_row);
			$prev=$row;
		}
		$count++;
	}

	//close the file handlers
	fclose($fp2);
	fclose($fp);

	return;
}

##############################################################################################################################
#function: function_displayGraph
#parameters:
#return:
#
# Function to read the contents of the recordings24.csv file and construct a line graph
#
###############################################################################################################################
function function_displayGraph(){
	// Dataset definition
	$DataSet = new pData;
	$DataSet->ImportFromCSV("./recordings24.csv",",",array(3,4,5,6,7,8,9,10,11,12,13,14,15,16,17),FALSE,0);
	$DataSet->AddAllSeries();
	$DataSet->SetAbsciseLabelSerie();
	$DataSet->SetSerieName("Sensor0","Serie3");
	$DataSet->SetSerieName("Sensor1","Serie4");
	$DataSet->SetSerieName("Sensor2","Serie5");
	$DataSet->SetSerieName("Sensor3","Serie6");
	$DataSet->SetSerieName("Sensor4","Serie7");
	$DataSet->SetSerieName("Sensor5","Serie8");
	$DataSet->SetSerieName("Sensor6","Serie9");
	$DataSet->SetSerieName("Sensor7","Serie10");
	$DataSet->SetSerieName("Sensor8","Serie11");
	$DataSet->SetSerieName("Sensor9","Serie12");
	$DataSet->SetSerieName("Sensor10","Serie13");
	$DataSet->SetSerieName("Sensor11","Serie14");
	$DataSet->SetSerieName("Sensor12","Serie15");
	$DataSet->SetSerieName("Sensor13","Serie16");
	$DataSet->SetSerieName("Sensor14","Serie17");
	$DataSet->SetSerieName("Sensor15","Serie18");
	$DataSet->SetYAxisName("Temperature");
	$DataSet->SetYAxisUnit("C");
	$DataSet->SetXAxisName("Date Time");

	// Initialise the graph
	$Test = new pChart(850,390);
	$Test->setFontProperties("./tahoma.ttf",8);
	$Test->setGraphArea(60,30,750,300);
	$Test->drawFilledRoundedRectangle(7,7,830,350,5,240,240,240);
	$Test->drawRoundedRectangle(5,5,830,350,5,230,230,230);
	$Test->drawGraphArea(255,255,255,TRUE);
	$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
	$Test->drawGrid(4,TRUE,230,230,230,50);

	// Draw the 0 line
	$Test->setFontProperties("./tahoma.ttf",6);
	$Test->drawTreshold(0,143,55,72,TRUE,TRUE);

	// Draw the line graph
	$Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
	$Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);

	// Finish the graph
	$Test->setFontProperties("./tahoma.ttf",8);
	$Test->drawLegend(760,20,$DataSet->GetDataDescription(),255,255,255);
	$Test->setFontProperties("./tahoma.ttf",10);
	$Test->drawTitle(60,22,"BENTO box Temperatures of last 24 Measurements",50,50,50,585);
	$Test->Render("bentoGraph.png");
	return;
}


$ServerName = "{imap.gmail.com:993/imap/ssl}INBOX"; // For a IMAP connection    (PORT 143)
$UserName = "eeriladm@gmail.com";  // UserName of the email account
$PassWord = "Sese123!";	// Password of the email account

$hostname = "localhost"; // usually is localhost
$db_user = "eeriladm_u1"; // database username
$db_password = "1LNDzk2b"; // database password
$database = "eeriladm_db1"; // provide database name
$db_table = "BENTOBOX1"; // table Name

$db = mysql_connect($hostname, $db_user, $db_password) or die("could not open database - try again Later!!");//connecting to mysql
mysql_select_db($database,$db);	//connecting to the database

$mbox = imap_open($ServerName, $UserName,$PassWord) or die("Could not open Mailbox - try again later!");

if ($hdr = imap_check($mbox)) {
	$msgCount = $hdr->Nmsgs;
} else {
	echo "failed";
}
echo "No of recent msgs :".$hdr->Recent;
$MN=$msgCount;
$overview=imap_fetch_overview($mbox,"1:$MN",0);
$size=sizeof($overview);
echo "<title>Bentobox1</title>
        <META HTTP-EQUIV=\"REFRESH\" CONTENT=\"300\">
        <h1>BentoBox1</h1><br><br> Select New Rate :
	<input type=\"radio\" name=\"rate\" value=\"Normal\">Normal
	<input type=\"radio\" name=\"rate\" value=\"Burst\">Burst
	&nbsp&nbsp&nbsp<input type=\"submit\" value=\"Submit\">";

echo "<br>The rows inserted in database are: <br><table border=\"1\" cellspacing=\"0\" width=\"582\">";
echo "<tr bgColor = \"#F0F0F0\"><th colspan=\"2\">TimeStamp</th><th colspan=\"2\">Battery (V)</th><th colspan=\"2\">Rate</th><th>Sensor 0</th><th>Sensor 1</th><th>Sensor 2</th><th>Sensor 3</th><th>Sensor 4</th><th>Sensor 5</th><th>Sensor 6</th><th>Sensor 7</th><th>Sensor 8</th><th>Sensor 9</th><th>Sensor 10</th><th>Sensor 11</th><th>Sensor 12</th><th>Sensor 13</th><th>Sensor 14</th><th>Sensor 15</th></tr>";


for($i=0;$i<$size;$i++){
	$j=0;
	$val=$overview[$i];
	$msg=$val->msgno;
	$from=$val->from;
	$date=$val->date;
	$subj=$val->subject;
	$seen=$val->seen;
	if($subj != "SBD Msg From Unit: 300234011614310"){
		continue;
	}
	if($seen == 1){
		continue;
	}

	$from = ereg_replace("\"","",$from);

	// MAKE DANISH DATE DISPLAY
	//list($dayName,$day,$month,$year,$time) = split(" ",$date);
	//$time = substr($time,0,5);
	//$date = $day ." ". $month ." ". $year . " ". $time;

	$attachments = extract_attachments($mbox, $msg);

	//echo "Msg No: ".$msg."<br>";
	//echo "From: ".$from."<br>";
	//echo "Date: ".$date."<br>";
	//echo "Subject: ".$subj."<br>";

	foreach($attachments as $singleAttachment){
		if($singleAttachment['filename']){
			//echo "attachment Name :".$singleAttachment['filename']."<br>";
			$measurement = $singleAttachment['attachment'];
		}
	}

	if(substr($measurement,10,1)=='B'){
		$timestamp = substr($measurement,0,10);
		$timestamp = substr($timestamp,4,2)."/".substr($timestamp,2,2)."/".substr($timestamp,0,2)." ".substr($timestamp,6,2).":".substr($timestamp,8);
		$batteryPercent = substr($measurement,11,4);
		$rate = substr($measurement,16,1);
		$temperatures = substr($measurement,18);
		$arrtemp = array();
		$arrtemp = split(",",$temperatures);
	}else{
		$timestamp = substr($measurement,0,9);
		$timestamp = substr($timestamp,3,2)."/".substr($timestamp,1,2)."/".substr($timestamp,0,1)." ".substr($timestamp,5,2).":".substr($timestamp,7);
		$batteryPercent = substr($measurement,10,4);
		$rate = substr($measurement,15,1);
		$temperatures = substr($measurement,17);
		$arrtemp = array();
		$arrtemp = split(",",$temperatures);
	}

       if($timestamp=="// :"){
                        continue;
                }
	if ($rate == 0){
		$rate = "Normal";
	}else if($rate == 1){
		$rate = "Burst";
	}

	if ($bgColor == "#FFFFFF") {
		$bgColor = "#F0F0F0";
	} else {
		$bgColor = "#FFFFFF";
	}

	echo "<tr bgcolor=\"$bgColor\"><td colspan=\"2\">$timestamp</td><td colspan=\"2\">$batteryPercent</td><td colspan=\"2\">$rate</td>";
	foreach($arrtemp as $temp){
			$arrtemp[$j] = (int) $temp/100;
			echo "<td align = \"center\">$arrtemp[$j]</td>";
			$j++;
		}
		while(sizeof($arrtemp)!=16){
            array_push($arrtemp,100);
		}

	echo "msgno: ".$msg." Seen: ".$seen."<br>";
	$sql = "INSERT INTO $db_table(RecordTime,BatteryPercent,Rate,Temperature1,Temperature2,Temperature3,Temperature4,Temperature5,Temperature6,Temperature7,Temperature8,Temperature9,Temperature10,Temperature11,Temperature12,Temperature13,Temperature14,Temperature15,Temperature16) values ('".$timestamp."','".$batteryPercent."','".$rate."','".$arrtemp[0]."','".$arrtemp[1]."','".$arrtemp[2]."','".$arrtemp[3]."','".$arrtemp[4]."','".$arrtemp[5]."','".$arrtemp[6]."','".$arrtemp[7]."','".$arrtemp[8]."','".$arrtemp[9]."','".$arrtemp[10]."','".$arrtemp[11]."','".$arrtemp[12]."','".$arrtemp[13]."','".$arrtemp[14]."','".$arrtemp[15]."')";
	if(!($result = mysql_query($sql ,$db))) {
		echo "ERROR: ".mysql_error()." at message no.: ".$msg."\n";
	}
	//echo $measurement;
	echo "</tr>";
}
imap_close($mbox);
mysql_close($db);
function_exportToCSV($hostname, $db_user, $db_password,$db_table,$database);
function_displayGraph();
mysql_close($db2);
?>