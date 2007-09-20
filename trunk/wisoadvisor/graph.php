<?php
include ("statistics/graph/src/jpgraph.php");
include ("statistics/graph/src/jpgraph_bar.php");

session_start();
$title = $_SESSION['title'];
$datay = $_SESSION['daten']['y'];
$datax = $_SESSION['daten']['x'];

// Setup the graph. 
$graph = new Graph(500,250);	
$graph->img->SetMargin(60,20,30,50);
$graph->SetScale("textlin");
$graph->SetMarginColor("white");
//$graph->SetShadow();

// Set up the title for the graph
$graph->title->Set($title);
$graph->title->SetFont(FF_VERDANA,FS_NORMAL,12);
$graph->title->SetColor("black");

// Setup font for axis
$graph->xaxis->SetFont(FF_VERDANA,FS_NORMAL,10);
$graph->yaxis->SetFont(FF_VERDANA,FS_NORMAL,10);

// Show 0 label on Y-axis (default is not to show)
$graph->yscale->ticks->SupressZeroLabel(true);
$graph->yaxis->SetLabelFormatCallback('yScaleCallback');


// Setup X-axis labels
$graph->xaxis->SetTickLabels($datax);
$graph->xaxis->SetLabelAngle(50);

// Create the bar pot
$bplot = new BarPlot($datay);
$bplot->SetWidth(0.5);

// Setup color for gradient fill style 
$bplot->SetFillGradient("#003366","#CCCCCC",GRAD_LEFT_REFLECTION);

// Set color for the frame of each bar
$bplot->SetColor("white");
$graph->Add($bplot);

// Finally send the graph to the browser
$graph->Stroke();  

function yScaleCallback($aVal) {
  return number_format($aVal, 0);
}


?>