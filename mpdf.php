<?php
    require_once("includes/session.php");
    require("mpdf/vendor/autoload.php");
    require("mpdf/qr/vendor/autoload.php");

    use Mpdf\QrCode\QrCode;
    use Mpdf\QrCode\Output;

    //instance of the pdf and qrcode class
    $pdf = new \Mpdf\Mpdf();

    function remakeNumber(string $number){
        //remove +233
        if(strlen($number) >= 12)
            $number = str_replace("+233", "0", $number);
        
        //insert spaces
        if(strlen($number) < 12){
            $number = str_split($number, 3);

            //set number in xxx xxx xxxx
            $number = $number[0]." ".$number[1]." ".$number[2].$number[3];
        }        

        return $number;
    }

    // $_GET["indexNumber"] = "012006601721";
    if((isset($_SESSION["ad_stud_index"]) && !empty($_SESSION["ad_stud_index"])) || isset($_GET["indexNumber"])){   
        //student lastname
        $lastname = $_SESSION["ad_stud_lname"];

        //set footer name with school name
        $school_name = $_SESSION["ad_school_name"];

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

        //provide document information
        $pdf->SetCreator("MatrixMe");
        $pdf->SetAuthor("SHSDesk");
        $pdf->SetTitle("Admission Letter | $lastname");
        $pdf->SetSubject("Admission Letter");
        $pdf->SetKeywords("Admission, SHSDesk, letter, document");

        //user data
        $date_now = date("M j, Y")." at ".date("H:i:sa");
        $enrolment_code = $_SESSION["ad_stud_enrol_code"];
        $candidate = $lastname." ".$_SESSION["ad_stud_oname"];
        $residence_status = $_SESSION["ad_stud_residence"];
        $program = $_SESSION["ad_stud_program"];
        $house = $_SESSION["ad_stud_house"];
        $gender = $_SESSION["ad_stud_gender"];

        if($gender == "Male"){
            $gender = "Sir";
        }else{
            $gender = "Madam";
        }

        if($house == "e"){
            $house = "Allocated Later";
        }
        
        $reopening = date("jS F, Y", strtotime($_SESSION["ad_reopening"]));

        $message = html_entity_decode($_SESSION["ad_message"]);

        $message = str_replace("\\r", "", $message);
        $message = str_replace("\\n", "", $message);
        $message = str_replace("\\", "", $message);
        $message = str_replace("<p></p>","", $message);

        $message .= "<br><span>Yours faithfully, </span>";

        //school details
        $head_master = $_SESSION["ad_school_head"];
        $it_name = $_SESSION["ad_it_admin"];
        $box_address = $_SESSION["ad_box_address"];
        $school_phone = remakeNumber($_SESSION["ad_school_phone"]);
        $logo = $_SESSION["ad_school_logo"];
        $logo = html_entity_decode("<img src=\"$url/$logo\" alt=\"logo\" width=\"30mm\" height=\"30mm\">", ENT_QUOTES);

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
            <div class="body">
                <br><span>Dear $gender</span>
                <div class="letter">
                    <h3 class="letter_title" style="text-align: center;"><u>Offer Of Admission</u></h3>
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
                <span class="it-admin">$it_name</span>
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

    close_connections();
?>