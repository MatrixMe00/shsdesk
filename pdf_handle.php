
<?php
    require_once("tcpdf/tcpdf.php");
    require_once("includes/session.php");
    
    //create a custom header
    class MYPDF extends TCPDF {
        // public function Header(){
        //     $image_file = "assets/images/default/thought-catalog-xHaZ5BW9AY0-unsplash.jpg";
        //     $this->SetFont("", 'B', 9);
        //     $this->SetTextColor(167, 147, 68);
        //     $this->Image($image_file, 11, 3, 20, 20);
        // }

        public function Footer(){
            $this->SetY(-10);
            $this->SetFont("", "B", 8);
            $this->Cell($this->GetCharWidth('Page '.$this->getPage()),10,'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages(),"T",false, 'L', 0, '', 0, false, 'T', 'M');
            $this->Cell(0,10,'SHSDesk','T',false, 'C', 0, '', 0, false, 'T', 'M');
            
        }
    }

    //provide data for admission letter
    if(isset($_SESSION["ad_stud_index"]) && !empty($_SESSION["ad_stud_index"])){
        //download prospectus first
        if($_SESSION["ad_school_prospectus"] != null){
?>
<head>
    <title>Download Documents</title>
    <script src="<?php echo $url?>/assets/scripts/jquery/uncompressed_jquery.js"></script>
    <style>
        a,button{
            display: none;
        }
        #span{
            width: 100vw;
            height: 100vh;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        body{
            overflow: hidden;
        }
    </style>
</head>

<body>
    <a href="<?php echo $url?>/admin/admin/assets/files/prospectus/Over 100 Biblical Names Of God – UrbanAreas.net.pdf" id="save">Click Me</a>
    <button id="btn"></button>
    <div id="span">
        <span>Prospectus downloaded successfully. Click anywhere to download admission form</span>
    </div>
    <?php 
        $_SESSION["ad_school_prospectus"] = null;
    ?>

    <script>
        $("#btn").click(function(){
            //click on save
            $("#save")[0].click();
        })

        $("#span").click(function(){
            location.reload();
        })

        $("#btn").click();
    </script>
</body>
<?php
        }else{
            //prevent admin logged in from signing out upon session destroy
            if(isset($_SESSION['user_login_id']) && $_SESSION['user_login_id'] > 0){
                $session_id = $_SESSION["user_login_id"];
                $login_id = $_SESSION["login_id"];
            }

            //create a new pdf document
            $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            //user lastname
            $lastname = $_SESSION["ad_stud_lname"];

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
            $reopening = "14th February, 2021";

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
            $school_phone = $_SESSION["ad_school_phone"];
            $school_name = $_SESSION["ad_school_name"];

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

            // //destroy session
            // session_destroy();

            // //set destroyed admin sessions
            // session_start();
            // $_SESSION["login_id"] = $login_id;
            // $_SESSION["user_login_id"] = $session_id;

            //output data
            $pdf->Output("Admission Form | $lastname.pdf");
        }
    }else{
        echo "No result to deliver";
    }
?>