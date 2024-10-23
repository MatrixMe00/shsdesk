<?php
    require_once("includes/session.php");
    require("mpdf/vendor/autoload.php");
    require("mpdf/qr/vendor/autoload.php");

    use Mpdf\QrCode\QrCode;
    use Mpdf\QrCode\Output;

    //instance of the pdf and qrcode class
    $pdf = new \Mpdf\Mpdf();

    // $_GET["indexNumber"] = "012006601721";
    if((isset($_SESSION["ad_stud_index"]) && !empty($_SESSION["ad_stud_index"])) || isset($_GET["indexNumber"])){   
        //student lastname
        $lastname = $_SESSION["ad_stud_lname"];

        //set footer name with school name
        $school_name = $_SESSION["ad_school_name"];

        $school = getSchoolDetail($school_name, true);

        if(empty($school["admission_template"])){
            //make settings for footer
            $footer = array (
                'odd' => array (
                    'L' => array (
                        'content' => 'Page {PAGENO} of {nbpg}',
                        'font-size' => 10,
                        'font-style' => 'R',
                        'font-family' => 'Times',
                        'color'=>'#000000'
                    ),
                    'C' => array (
                        'content' => "$school_name",
                        'font-size' => 10,
                        'font-style' => 'R',
                        'font-family' => 'Times',
                        'color'=>'#000000'
                    ),
                    'line' => 1
                ),
                'even' => array (
                    'L' => array (
                        'content' => 'Page {PAGENO} of {nbpg}',
                        'font-size' => 10,
                        'font-style' => 'R',
                        'font-family' => 'Times',
                        'color'=>'#000000'
                    ),
                    'C' => array (
                        'content' => "$school_name",
                        'font-size' => 10,
                        'font-style' => 'R',
                        'font-family' => 'Times',
                        'color'=>'#000000'
                    ),
                    'line' => 1
                )
            );

            //apply footer settings
            $pdf->setFooter($footer);

            // some options which are a must here
            $date_now = date("M j, Y")." at ".date("H:i:sa");
            $school_phone = remakeNumber($_SESSION["ad_school_phone"]);
            $logo = $_SESSION["ad_school_logo"];
            $logo = html_entity_decode("<img src=\"$url/$logo\" alt=\"logo\" width=\"30mm\" height=\"30mm\">", ENT_QUOTES);
        }else{
            $template = $pdf->setSourceFile("$rootPath/{$school['admission_template']}");
            $template = $pdf->importPage($template);

            // use imported template
            $pdf->useTemplate($template);
            $pdf->SetY(60);
            $date_now = date("jS F, Y");
        }
        
        //provide document information
        $pdf->SetCreator("MatrixMe");
        $pdf->SetAuthor("SHSDesk");
        $pdf->SetTitle("Admission Letter | $lastname");
        $pdf->SetSubject("Admission Letter");
        $pdf->SetKeywords("Admission, SHSDesk, letter, document, $lastname, MatrixMe");

        //user data
        $enrolment_code = $_SESSION["ad_stud_enrol_code"];
        $candidate = $lastname." ".$_SESSION["ad_stud_oname"];
        $residence_status = $_SESSION["ad_stud_residence"];
        $program = $_SESSION["ad_stud_program"];
        $house = $_SESSION["ad_stud_house"];
        $gender = $_SESSION["ad_stud_gender"] == "Male" ? "Sir" : "Madam";
        $profile_pic = $_SESSION["ad_profile_pic"];

        if(!empty($profile_pic)){
            $profile_pic = "<img src=\"$url/$profile_pic\" width=\"100%\" height=\"100%\">";
            $profile_pic = html_entity_decode($profile_pic, ENT_QUOTES);
        }

        if($house == "e"){
            $house = "Allocated Later";
        }
        
        $reopening = date("jS F, Y", strtotime($_SESSION["ad_reopening"]));

        $message = html_entity_decode($_SESSION["ad_message"]);
        $message = str_replace(PHP_EOL, '', $message);

        $message .= "<br><span>Yours faithfully, </span>";

        //school details
        $head_master = $_SESSION["ad_school_head"];
        $it_name = $_SESSION["ad_it_admin"];
        $box_address = $_SESSION["ad_box_address"];
        $ad_title = $_SESSION["ad_admission_title"];

        //qrcode
        $newqr = '<barcode code="Document digitally signed by '.$head_master.'" type="QR" class="barcode" size="1" error="M" disableborder="0"
            style="background-color: yellow;" />';

        //document display body
        $html = <<<HTML
        <head>
            <style>
                h1,h2,h3,h4,h5,h6{
                    line-height: 1.3;
                }
                div{
                    line-height: 1.3;
                }
                .demo_text{
                    line-height: 1.3;
                    color: red;
                }
                .school_name{
                    font-weight:bold;
                }
                .digital{
                    margin-top: 3mm;
                }
            </style>
        </head>
        HTML;

        if(empty($school["admission_template"])){
            $html .= <<<HTML
            <div class="header" style="text-align: center;">
                <div>
                    <br>$logo
                </div>
                <span class="school_name">$school_name</span><br>
                <span>$box_address</span><br>
                <span>Telephone: $school_phone</span>
            </div>
            <div class="middle">
                <div class="print_date" style="border-bottom: 1px solid lightgrey; padding-bottom: 5px;">
                    <span>Printed: $date_now</span>
                </div>
            HTML;
        }else{
            $html .= <<<HTML
            <div class="middle">
                <p class="issue_date" style="text-align: right"><b>$date_now</b></p>
            HTML;
        }
        
        $html .= <<<HTML
            <div class="body">
                <br><span>Dear $gender</span>
                <div class="letter">
                    <h3 class="letter_title" style="text-align: center; font-size: 11pt"><u>$ad_title</u></h3>
                    <div style="float: right; width: 30mm; height: 30mm">
                        $profile_pic
                    </div>
                    <div class="candidate_info" style="text-align: start;">
                        <span>Enrolment Code: $enrolment_code</span><br>
                        <span>Candidate Name: $candidate</span><br>
                        <span>Residence Status: $residence_status</span><br>
                        <span>Program Offered: $program</span><br>
                        <span>House Allocated: $house</span><br>
                        <span>Reopening Date: $reopening</span><br>
                    </div>
                    <div class="message">
                        $message
                    </div>
                </div> 
            </div>
        </div>
        <div class="foot" class="digital" style="border-left: 2px solid lightgrey; text-align: right">
            <div style="text-align:left; padding-left: 4mm">$newqr</div>
            <div style="padding-top: -17mm">
                <span>
                    <i>--Digitally Signed in QR Code--</i>
                </span><br>
                <span class="headMaster">$head_master</span><br>
                <span class="it-admin">[School Head]</span>
            </div>
        </div>
        HTML;

        //write some html
        $pdf->writeHTML($html);

        //output html
        $pdf->Output("Admission Form | $lastname.pdf", "D");
    }else{
        echo "No result to deliver";
    }
?>