<?php 
// Standard inclusions
include("../pData.class");
include("../pChart.class");

function function_12RowTable($inputFile, $outputFile){
	$fp = fopen($inputFile,'r');
	$fp2 = fopen($outputFile,'w') or die('cant open file');
	$dataFromFile = array_slice(file($inputFile), -12);
        foreach ($dataFromFile as $data) {
			fwrite($fp2,$data."\n");
		}
	fclose($fp2);
	fclose($fp);
return;
}

function function_displayGraph($position){
// Dataset definition      
 $DataSet = new pData;     
 $DataSet->ImportFromCSV("./recordings312.csv",",",array($position),FALSE,0);     
 $DataSet->AddAllSeries();     
 $DataSet->SetAbsciseLabelSerie();     
 if($position==3){
	$DataSet->SetSerieName("WindSpeed","Serie3");
	$DataSet->SetYAxisName("WindSpeed");  
	$DataSet->SetYAxisUnit("m/s");    
 }else if ($position==4){
	$DataSet->SetSerieName("WindDirection","Serie4");
	$DataSet->SetYAxisName("WindDirection");  
	$DataSet->SetYAxisUnit("Deg");
 }else if ($position==9){
	$DataSet->SetSerieName("CO2","Serie11");
	$DataSet->SetYAxisName("CO2");  
	$DataSet->SetYAxisUnit("ppm");
 }else if ($position==10){
	$DataSet->SetSerieName("SO2","Serie12");
	$DataSet->SetYAxisName("SO2");  
	$DataSet->SetYAxisUnit("ppm");
 }
 $DataSet->SetXAxisName("Time"); 
   
 // Initialise the graph     
 $Test = new pChart(600,390);     
 $Test->setFontProperties("../tahoma.ttf",8);     
 $Test->setGraphArea(60,30,550,300);     
 $Test->drawFilledRoundedRectangle(7,7,600,350,5,240,240,240);     
 $Test->drawRoundedRectangle(5,5,600,350,5,230,230,230);     
 $Test->drawGraphArea(255,255,255,TRUE);  
 $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);     
 $Test->drawGrid(4,TRUE,230,230,230,50);  
   
 // Draw the 0 line     
 $Test->setFontProperties("../tahoma.ttf",6);     
 $Test->drawTreshold(0,160,55,72,TRUE,TRUE);     
   
 // Draw the line graph  
 $Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());     
 $Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);     
   
 // Finish the graph     
 $Test->setFontProperties("../tahoma.ttf",8);     
 $Test->drawLegend(450,40,$DataSet->GetDataDescription(),255,255,255);     
 $Test->setFontProperties("../tahoma.ttf",10);     
 if($position==3){
	$Test->drawTitle(60,22,"BENTO box last 12 Measurements of Wind Speeds",50,50,50,585);     
	$Test->Render("bentoWSsmallGraph.png");  
 }else if ($position==4){
	$Test->drawTitle(60,22,"BENTO box last 12 Measurements of Wind Directions",50,50,50,585);     
	$Test->Render("bentoWDsmallGraph.png");
 }else if ($position==11){
	$Test->drawTitle(60,22,"BENTO box last 12 Measurements of CO2",50,50,50,585);     
	$Test->Render("bentoCO2smallGraph.png");
 }else if ($position==12){
	$Test->drawTitle(60,22,"BENTO box last 12 Measurements of SO2",50,50,50,585);     
	$Test->Render("bentoSO2smallGraph.png");
 }
return;
}

echo "<html>
<head>
<title>Bentobox Nicaragua</title>
<link href=\"http://extremerobotics.lab.asu.edu/Bentobox/bentoStyle2.css\" rel=\"stylesheet\" type=\"text/css\" />
</head>

<body>

<div id=\"header\">Behar's Environmental Networking, Telemetry and Observation Box</div>
<div id='subtitle'>(BENTO box 3) <br />
<table border=\"1\" width = \"100%\" >
	<tr align='center' font-size=30>
		<td><a href=\"http://extremerobotics.lab.asu.edu/Bentobox/Bentobox3/bentoNicaraguaHome.html\">Home</a></td>
		<td><a href=\"http://extremerobotics.lab.asu.edu/Bentobox/Bentobox3/bentoNicaraguaGeneral.php\">Overview</a></td>
		<td><a href=\"http://extremerobotics.lab.asu.edu/Bentobox/Bentobox3/bentoNicaraguaWindSpeed.php\">Wind Speed</a></td>
		<td><a href=\"http://extremerobotics.lab.asu.edu/Bentobox/Bentobox3/bentoNicaraguaWindDirection.php\">Wind Direction</a></td>
		<td><a href=\"http://extremerobotics.lab.asu.edu/Bentobox/Bentobox3/bentoNicaraguaCO2.php\">CO2</a></td>
		<td><a href=\"http://extremerobotics.lab.asu.edu/Bentobox/Bentobox3/bentoNicaraguaSO2.php\">SO2</a></td>
		<td><a href=\"http://extremerobotics.lab.asu.edu/Bentobox/Bentobox3/recordings3.csv\">Download csv</a></td>
	</tr>
</table>
</div>";

function_12RowTable("./recordings324.csv","./recordings312.csv");
function_displayGraph(3);
function_displayGraph(4);
function_displayGraph(9);
function_displayGraph(10);
echo "
<div id=\"middle1\">
    <div id=\"left\"><img src=\"bentoWSsmallGraph.png\"></div>
	<div id=\"right\"><img src=\"bentoWDsmallGraph.png\"></div>
</div>
<div id=\"middle2\">
    <div id=\"left\"><img src=\"bentoCO2smallGraph.png\"></div>
	<div id=\"right\"><img src=\"bentoSO2smallGraph.png\"></div>
</div>
<div id=\"footer\">
			<a href=\"mailto:amoravar@asu.edu\">Contact us</a>
			</div>
</body>
</html>";
?>