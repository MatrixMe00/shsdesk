<?php
// class CustomPdfGenerator extends TCPDF 
// {
//     public function Header() 
//     {
//         $image_file = '/web/logo.png';
//         $this->Image($image_file, 10, 3, 25, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
//         $this->SetFont('helvetica', 'B', 20);
//         $this->Cell(0, 15, '', 0, false, 'C', 0, '', 0, false, 'M', 'M');
//         $this->Ln();
//         $this->Cell(0, 15, 'Katie A Falk', 0, false, 'R', 0, '', 0, false, 'M', 'M');
//     }
 
//     public function Footer() 
//     {
//         $this->SetY(-15);
//         $this->SetFont('helvetica', 'I', 15);
//         $this->Cell(0, 10, 'Thank you for your business!', 0, false, 'C', 0, '', 0, false, 'T', 'M');
//     }
 
//     public function printTable($header, $data)
//     {
//         $this->SetFillColor(0, 0, 0);
//         $this->SetTextColor(255);
//         $this->SetDrawColor(128, 0, 0);
//         $this->SetLineWidth(0.3);
//         $this->SetFont('', 'B', 12);
 
//         $w = array(110, 17, 25, 30);
//         $num_headers = count($header);
//         for($i = 0; $i < $num_headers; ++$i) {
//             $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
//         }
//         $this->Ln();
 
//         // Color and font restoration
//         $this->SetFillColor(224, 235, 255);
//         $this->SetTextColor(0);
//         $this->SetFont('');
 
//         // table data
//         $fill = 0;
//         $total = 0;
 
//         foreach($data as $row) {
//             $this->Cell($w[0], 6, $row[0], 'LR', 0, 'L', $fill);
//             $this->Cell($w[1], 6, $row[1], 'LR', 0, 'R', $fill);
//             $this->Cell($w[2], 6, number_format($row[2]), 'LR', 0, 'R', $fill);
//             $this->Cell($w[3], 6, number_format($row[3]), 'LR', 0, 'R', $fill);
//             $this->Ln();
//             $fill=!$fill;
//             $total+=$row[3];
//         }
 
//         $this->Cell($w[0], 6, '', 'LR', 0, 'L', $fill);
//         $this->Cell($w[1], 6, '', 'LR', 0, 'R', $fill);
//         $this->Cell($w[2], 6, '', 'LR', 0, 'L', $fill);
//         $this->Cell($w[3], 6, '', 'LR', 0, 'R', $fill);
//         $this->Ln();
 
//         $this->Cell($w[0], 6, '', 'LR', 0, 'L', $fill);
//         $this->Cell($w[1], 6, '', 'LR', 0, 'R', $fill);
//         $this->Cell($w[2], 6, 'TOTAL:', 'LR', 0, 'L', $fill);
//         $this->Cell($w[3], 6, $total, 'LR', 0, 'R', $fill);
//         $this->Ln();
 
//         $this->Cell(array_sum($w), 0, '', 'T');
//     }
// }

    require_once("tcpdf/tcpdf.php");
    require_once("includes/session.php");

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

    //create a custom header

    class MYPDF extends TCPDF {
        // public function Header(){
        //     $image_file = "assets/images/default/thought-catalog-xHaZ5BW9AY0-unsplash.jpg";
        //     $this->SetFont("", 'B', 9);
        //     $this->SetTextColor(167, 147, 68);
        //     $this->Image($image_file, 11, 3, 20, 20);
        // }
        
        private $name;

        public function setFooterSchoolName($name){
            $this->name = $name;
        }

        public function Footer(){
            $this->SetY(-10);
            $this->SetFont("", "B", 8);
            $this->Cell($this->GetCharWidth('Page '.$this->getPage()),10,'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages(),"T",false, 'L', 0, '', 0, false, 'T', 'M');
            $this->Cell(0,10,$this->name,'T',false, 'C', 0, '', 0, false, 'T', 'M');
            
        }
    }

    if(isset($_SESSION["ad_stud_index"]) && !empty($_SESSION["ad_stud_index"])){
        $session_set = false;
        //prevent admin logged in from signing out upon session destroy
        // if(isset($_SESSION['user_login_id']) && $_SESSION['user_login_id'] > 0){
        //     $session_id = $_SESSION["user_login_id"];
        //     $login_id = $_SESSION["login_id"];
        //     $nav_point = $_SESSION["nav_point"];
        //     $session_set = true;
        // }

        //create a new pdf document
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        //user lastname
        $lastname = $_SESSION["ad_stud_lname"];

        //set footer name with school name
        $school_name = $_SESSION["ad_school_name"];

        $pdf->setFooterSchoolName($school_name);

        //set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('SHSDesk');
        $pdf->SetTitle('Admission Letter | '.$lastname);
        $pdf->SetSubject('Admission Letter');
        $pdf->SetKeywords('Admission, SHSDesk, letter, document');

        //add a page
        $pdf->AddPage();

        //set font
        $pdf->SetFont('Times','', 12);

        //user data
        $date_now = date("M j, Y")." at ".date("H:i:sa");
        $enrolment_code = $_SESSION["ad_stud_enrol_code"];
        $candidate = $lastname." ".$_SESSION["ad_stud_oname"];
        $residence_status = $_SESSION["ad_stud_residence"];
        $program = $_SESSION["ad_stud_program"];
        $house = $_SESSION["ad_stud_house"];
        $reopening = "Reopening Date";

        // $message = $_SESSION["ad_message"];
        $message = "
            <span class=\"demo_text\">[ -- Start of body text </span><br><p>The message in this square brackets is what you will provide as your admission letter. Please do well to fill out the message so as to automatically generate the admission form for your students</p>
            <p>Demo paragraph begins. Video provides a powerful way to help you prove your point. When you click Online Video, you can paste in the embed code for the video you want to add. You can also type a keyword to search online for the video that best fits your document.</p>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Saepe inventore, iste quibusdam quis recusandae quia sed unde id odit quos, dignissimos consequatur ullam totam accusamus sequi dicta laboriosam maxime molestiae?</p>

            <ol>
                <li>Provide lists where the need be. Ad cupiditate repudiandae minus!</li>
                <li>Demo list begins on this line too.Lorem ipsum dolor sit amet. Tenetur, doloribus.</li>
                <li>Demo list item Lorem ipsum dolor sit amet, consectetur adipisicing elit. Est consectetur commodi veritatis!</li>
                <li>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Est consectetur commodi veritatis!</li>
            </ol><span class=\"demo_text\">End of body text -- ]</span><br>

            <span>Yours Faithfully, </span>
        ";

        //school details
        $head_master = $_SESSION["ad_school_head"];
        $it_name = $_SESSION["ad_it_admin"];
        $box_address = $_SESSION["ad_box_address"];
        $school_phone = remakeNumber($_SESSION["ad_school_phone"]);

        $html = <<<HTML
        <head>
            <style>
                span{
                    line-height: 8px;
                }
                h1,h2,h3,h4,h5,h6{
                    line-height: 10px;
                }
                div{
                    line-height: 15px;
                }

                .demo_text{
                    line-height: 5px;
                    color: red;
                }

                .school_name{
                    font-weight:bold;
                }
            </style>
        </head>
        <div class="header" style="text-align: center;">
            <div>
                <br><img src="$url/assets/images/default/thought-catalog-xHaZ5BW9AY0-unsplash.jpg" alt="logo" width="30mm" height="30mm">
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
                <br><span>Dear Sir / Madam</span>
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
        <div class="foot" class="digital" style="border-left: 2px solid lightgrey; text-align: right;">
            <span>
                <i>--Digitally Signed in QR Code--</i>
            </span><br>
            <span class="headMaster">$head_master</span><br>
            <span class="it-admin">$it_name</span>
        </div>
        HTML;

        $tagvs = array(
            'p' => array(
                0 => array(
                    'n' => 0,
                    'h' => ''
                ),
                1 => array(
                    'n' => 1,
                    'h' => ''
                )
            ),
            'div' => array(
                0 => array(
                    'n' => 0,
                    'h' => ''
                ),
                1 => array(
                    'n' => 0,
                    'h' => ''
                )
            ),
            'ol' => array(
                0 => array(
                    'n' => 0,
                    'h' => ''
                ),
                1 => array(
                    'n' => 1,
                    'h' => ''
                )
            )
        );

        //format empty spaces
        $pdf->setHtmlVSpace($tagvs);

        //write html text
        $pdf->writeHTMLCell(0,0,'','',$html,0,1,0,true,'',true);

        //qrcode style
        $barcode_style = array(
            'border' => 2,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => array(255,255,64)
        ); 

        //last cell height
        $h = $pdf->getLastH();
        $y = ($h * 95) / 100;

        if($pdf->getNumPages() > 1){
            $doc_height = $pdf->getPageHeight($pdf->getNumPages()) % $pdf->getPageHeight();
            
            if($y < ($doc_height * 10) / 100){
                //set current page to previous page
                $pdf->setPage($pdf->getNumPages() - 1);

                //move to 95% here
                $y = (($doc_height * 95) / 100) + (($doc_height * 9) / 100);
            }
        }

        //add qrcode
        $pdf->write2DBarcode("Signed by ".$head_master,"QRCODE,H",15, $y, '20','20',$barcode_style,"L");

        //writing data into pdf  
        $pdf->Text(20, 210, '');

        //destroy session
        // session_destroy();

        //set destroyed admin sessions
        if($session_set){
            session_start();
            $_SESSION["login_id"] = $login_id;
            $_SESSION["user_login_id"] = $session_id;
            $_SESSION["nav_point"] = $nav_point;
        }

        //output data
        $pdf->Output("Admission Form | $lastname.pdf");
    }else{
        echo "No result to deliver";
    }
?>