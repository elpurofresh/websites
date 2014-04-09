<?php
$hostname = "localhost"; // usually is localhost
$db_user = "eeriladm_u1"; // database username
$db_password = "1LNDzk2b"; // database password
$database = "eeriladm_db1"; // provide database name
$db_table = "Nicaragua1"; // table Name

$db = mysql_connect($hostname, $db_user, $db_password) or die("could not open database - try again Later!!");//connecting to mysql
mysql_select_db($database,$db);	//connecting to the database

echo "
<html>
<head>
<META HTTP-EQUIV=\"REFRESH\" CONTENT=\"300\">
<title>Bentobox Nicaragua Wind Speed</title>
<link href=\"http://extremerobotics.lab.asu.edu/Bentobox/bentoStyle.css\" rel=\"stylesheet\" type=\"text/css\" />
</head>

<body>

<div id=\"header\">Behar's Environmental Networking, Telemetry and Observation Box</div>
<div id='subtitle'>(BENTO box 3) <br />
<table border='1' width = '100%' >
	<tr align='center' font-size=30>
		<td width = '14.28%'><a href='http://extremerobotics.lab.asu.edu/Bentobox/Bentobox3/bentoNicaraguaHome.html'>Home</a></td>
		<td width = '14.28%'><a href='http://extremerobotics.lab.asu.edu/Bentobox/Bentobox3/bentoNicaraguaGeneral.php'>Overview</a></td>
		<td width = '14.28%'><a href='http://extremerobotics.lab.asu.edu/Bentobox/Bentobox3/bentoNicaraguaWindDirection.php'>Wind Direction</a></td>
		<td width = '14.28%'><a href='http://extremerobotics.lab.asu.edu/Bentobox/Bentobox3/bentoNicaraguaWindSpeed.php'>Wind Speed</a></td>
		<td width = '14.28%'><a href='http://extremerobotics.lab.asu.edu/Bentobox/Bentobox3/bentoNicaraguaCO2.php'>CO2</a></td>
		<td width = '14.28%'><a href='http://extremerobotics.lab.asu.edu/Bentobox/Bentobox3/bentoNicaraguaSO2.php'>SO2</a></td>
		<td width = '14.28%'><a href='http://extremerobotics.lab.asu.edu/Bentobox/Bentobox3/recordings3.csv'>Download csv</a></td>	
	</tr>
</table>
</div>
<div id=\"middle3\">
    <div id=\"left\"><iframe width=\"400\" height=\"290\" frameborder=\"0\" scrolling=\"no\" marginheight=\"0\" marginwidth=\"0\" src=\"http://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=telica+volcano&amp;aq=&amp;sll=14.248411,-84.935303&amp;sspn=2.89071,4.938354&amp;ie=UTF8&amp;hq=&amp;hnear=Telica&amp;ll=12.602,-86.845&amp;spn=0.181938,0.308647&amp;t=h&amp;z=12&amp;output=embed\"></iframe><br />Location: Telica, Nicaragua<br />Frequency Sampling Control :
				<form name='frequencySample' method='POST' action='../sendMail.php'>
				<input type=\"radio\" name=\"rate\" value=\"Normal\">Normal
				<input type=\"radio\" name=\"rate\" value=\"Burst\">Burst
				&nbsp&nbsp&nbsp<input type=\"submit\" value=\"Submit\"><br />
                                To download all the recordings of bentobox Click <a href='http://extremerobotics.lab.asu.edu/Bentobox/Bentobox3/recordings3.csv'>here!</a> </div>
    <div id=\"right\"><img src=\"bentoWindDirectionGraph.png\"></div>
</div>

<div id=\"middle4\"> The Bentobox3 Readings are: <br>
<table font-size='15px' border=\"1\" cellspacing=\"0\" width=\"96%\">
<tr bgColor = \"#F0F0F0\">
		<th colspan=\"2\">TimeStamp</th>
		<th colspan=\"2\">Battery (V)</th>
		<th colspan=\"2\">Rate</th>
		<th colspan=\"2\">Wind direction average (Degrees)</th>
		<th colspan=\"2\">Wind Speed average (m/s)</th>
		<th colspan=\"2\">Air Temperature (C)</th>
		<th colspan=\"2\">Relative Humidity (%RH)</th>
		<th colspan=\"2\">Air Pressure (hPa)</th>
		<th colspan=\"2\">Rain accumulation (mm)</th>
		<th colspan=\"2\">CO2 in ppm</th>
		<th colspan=\"2\">SO2 in ppm</th>
	</tr>";

// Get all the data from the "example" table
$result = mysql_query("SELECT * FROM  `Nicaragua1` ORDER BY  `Nicaragua1`.`RecordTime` ASC ") or die(mysql_error());

while($row = mysql_fetch_array($result)) {
	// Print out the contents of each row into a table
	if ($bgColor == "#FFFFFF") {
		$bgColor = "#F0F0F0";
	} else {
		$bgColor = "#FFFFFF";
	}

	echo "<tr bgcolor=\"$bgColor\" align='center'>";
	for($col=0; $col<11;$col++){
		if($col==3){
			echo "<td bgcolor='#0066FF' colspan=\"2\"> <font color='#FFFFFF' font-style='Bold'>$row[$col] </font></td>";
		}else{
			echo "<td  colspan=\"2\"> $row[$col] </td>";
		}
	}
	echo "</tr>";
}
echo "</table></div>
<div id=\"footer\">
			<a href=\"mailto:amoravar@asu.edu\">Contact us</a>
			</div>
</body>
</html>";
?>