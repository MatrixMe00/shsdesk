<?php
    require_once("tcpdf/tcpd.php");

    //create a new pdf document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    //prevent print header
    $pdf->setPrintHeader(false);

    //add a page
    $pdf->AddPage();

    //user data
    $html;

    //writing data into pdf
    $pdf->writeHTMLCell(0,0,'','',$html,0,1,0,true,'',true);
?>