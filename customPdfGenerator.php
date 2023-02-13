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
        $pdf->SetKeywords("Admission, SHSDesk, letter, document, $lastname, MatrixMe");

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
        $message = str_replace(PHP_EOL, '', $message);

        $message .= "<br><span>Yours faithfully, </span>";

        //school details
        $head_master = $_SESSION["ad_school_head"];
        $it_name = $_SESSION["ad_it_admin"];
        $box_address = $_SESSION["ad_box_address"];
        $school_phone = remakeNumber($_SESSION["ad_school_phone"]);
        $logo = $_SESSION["ad_school_logo"];
        $logo = html_entity_decode("<img src=\"$url/$logo\" alt=\"logo\" width=\"30mm\" height=\"30mm\">", ENT_QUOTES);
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
                    <h3 class="letter_title" style="text-align: center;"><u>$ad_title</u></h3>
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
    }elseif($_REQUEST["admission_print"] && $_REQUEST["admission_print"] != null){
        $html = $_REQUEST["html"];
        $index = $_REQUEST["ad_index"];

        //empty spots should be indicated as empty
        $html = str_replace("><", ">Not Defined<", $html);

        // echo $html; return;

        $index = fetchData("*","cssps","indexNumber='$index'");

        if(!is_array($index)){
            echo "<script>alert('Error encountered. Index Number is invalid or no data was fetched');</script>";
            exit(1);
        }

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
                    'content' => getSchoolDetail(intval($index["schoolID"]))["schoolName"],
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
                    'content' => getSchoolDetail(intval($index["schoolID"]))["schoolName"],
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
        $pdf->SetTitle("Admission Summary | ".$index["Lastname"]);
        $pdf->SetSubject("Admission Summary");
        $pdf->SetKeywords("Admission, SHSDesk, summary, document");

        $date_now = date("M j, Y")." at ".date("H:i:sa");

        $content = <<<HTML
        <head>
            <style>
                fieldset{
                    display: block;
                    margin-bottom: 1cm;
                    border: 1px solid black;
                }
                .joint{
                    margin: 5px;
                }

                .joint .label{
                    float: left;
                    clear: right;
                    width: 47%;
                    border: 1px solid lightgrey;
                    margin: 1px 5px;
                    padding: 5px;
                    min-height: 40px;
                }

                .label .value{
                    color: #222;
                    font-variant: small-caps;
                }

                .ng-hide{
                    display: none;
                }
            </style>
        </head>
        <form>
            $html
        </form>
        <br><hr><p>Print Date: $date_now</p>
        HTML;

        //write some html
        $pdf->writeHTML($content);

        //output html
        $pdf->Output("Admission Summary | ".$index["Lastname"].".pdf", "D");
    }else{
        echo "No result to deliver";
    }
?>