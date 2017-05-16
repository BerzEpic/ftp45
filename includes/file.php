<?php

include(plugin_dir_path( __FILE__ )."fpdf/fpdf.php"); 
include(plugin_dir_path( __FILE__ )."fpdi/fpdi.php"); 

// initiate FPDI 
$pdf =& new FPDI(); 
// add a page 
$pdf->AddPage(); 
// set the sourcefile 
$pdf->setSourceFile(plugin_dir_path( __FILE__ )."structure.pdf"); 
// import page 1 
$tplIdx = $pdf->importPage(1); 
// use the imported page as the template 
$pdf->useTemplate($tplIdx, 0, 0); 

// now write some text above the imported page 
$pdf->SetFont('Arial'); 
$pdf->SetTextColor(255,0,0); 
$pdf->SetXY(150, 25); 
$pdf->Write(0, "This is just a simple text"); 

$pdf->Output('newpdf.pdf', 'F'); 



?>