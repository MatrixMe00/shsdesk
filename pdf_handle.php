<?php
    require_once("includes/session.php");

    //provide links for admission letter and prospectus
    if((isset($_SESSION["ad_stud_index"]) && !empty($_SESSION["ad_stud_index"])) || isset($_GET["indexNumber"])){

        //if user enters via url or already enroled
        if(isset($_GET["indexNumber"]) && !empty($_GET["indexNumber"])){
            //create required session objects
            $student = fetchData("c.*, e.enrolCode, e.admission_number, e.reopening_date, e.enrolDate","cssps c JOIN enrol_table e ON c.indexNumber = e.indexNumber", "c.indexNumber='".$_GET["indexNumber"]."'");
            $school = fetchData("s.*, a.reopeningDate, a.letter_prefix", "schools s JOIN admissiondetails a ON s.id = a.schoolID", "s.id=".$student["schoolID"], join_type: "left");

            //details for school
            $_SESSION["ad_school_name"] = $school["schoolName"];
            $_SESSION["ad_box_address"] = $school["postalAddress"];
            $_SESSION["ad_school_prospectus"] = $school["prospectusPath"];

            if($school["techContact"][0] != "0" && $school["techContact"][0] != "+"){
                $_SESSION["ad_school_phone"] = "+".$school["techContact"];
            }else{
                $_SESSION["ad_school_phone"] = $school["techContact"];
            }
            
            $_SESSION["ad_school_head"] = $school["headName"];
            $_SESSION["ad_it_admin"] = $school["techName"];
            $_SESSION["ad_message"] = $school["admissionPath"];
            $_SESSION["ad_school_logo"] = $school["logoPath"];
            $_SESSION["ad_admission_title"] = $school["admissionHead"];
            $_SESSION["ad_reopening"] = $student["reopening_date"];

            //student details
            $_SESSION["ad_stud_index"] = $student["indexNumber"];
            $_SESSION["ad_stud_lname"] = $student["Lastname"];
            $_SESSION["ad_stud_oname"] = $student["Othernames"];
            $_SESSION["ad_stud_enrol_code"] = $student["enrolCode"];
            $_SESSION["ad_stud_residence"] = $student["boardingStatus"];
            $_SESSION["ad_stud_program"] = $student["programme"];
            $_SESSION["ad_stud_gender"] = $student["Gender"];
            $_SESSION["ad_profile_pic"] = $student["profile_pic"];

            // ensure student's admission number is stored in db
            if(!$student["admission_number"] && !empty($school["letter_prefix"])){
                $admission_number = generateAdmissionNumber($student["schoolID"], $school["letter_prefix"], $student["enrolDate"]);
                
                //update admission number in db
                $sql = "UPDATE enrol_table SET admission_number=? WHERE indexNumber=?";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("ss", $admission_number, $student["indexNumber"]);
                $stmt->execute();

                $_SESSION["ad_admission_number"] = $admission_number;
            }else{
                $_SESSION["ad_admission_number"] = $student["admission_number"];
            }

            if(is_array(fetchData("houseID","house_allocation","indexNumber='".$student["indexNumber"]."'"))){
                $house_id = fetchData("houseID","house_allocation","indexNumber='".$student["indexNumber"]."'")["houseID"];
                if(!is_null($house_id)){
                    $_SESSION["ad_stud_house"] = fetchData("title","houses","id=$house_id")["title"];
                }else{
                    $_SESSION["ad_stud_house"] = "e";
                }
                
                $houses = 1;
            }else{
                //check if student is allocated a house
                $allocated = fetchData("indexNumber","house_allocation","indexNumber='".$_GET["indexNumber"]."'");
                
                if(!is_array($allocated) && !empty($student["boardingStatus"])){
                    //insert data into database
                    $sql = "INSERT INTO `house_allocation` (`indexNumber`, `schoolID`, `studentLname`, `studentOname`, `houseID`, `studentYearLevel`, `studentGender`, `boardingStatus`) VALUES (?, ?, ?, ?, NULL, 1, ?, ?)";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("sissss",$_GET["indexNumber"], $student["schoolID"], $student["Lastname"], $student["Othernames"], $student["Gender"], $student["boardingStatus"]);
                    $stmt->execute();
                }
                
                //set houses to 0
                $houses = 0;
            }
        }else{
            if(is_array(fetchData("houseID","house_allocation","indexNumber='".$_SESSION["ad_stud_index"]."'"))){
                if(!isset($_SESSION["ad_stud_house"]) || $_SESSION["ad_stud_house"] !== "e"){
                    $_SESSION["ad_stud_house"] = fetchData("title","houses","id=".fetchData("houseID","house_allocation","indexNumber='".$_SESSION["ad_stud_index"]."'")["houseID"])["title"];
                }
                
                $houses = 1;
            }else{
                //check if student is allocated a house
                $allocated = fetchData("indexNumber","house_allocation","indexNumber='".$_SESSION["ad_stud_index"]."'");
                $student = fetchData("c.*, e.enrolCode","cssps c JOIN enrol_table e ON c.indexNumber = e.indexNumber", "c.indexNumber='".$_SESSION["ad_stud_index"]."'");
                
                if(!is_array($allocated)){
                    //insert data into database
                    $sql = "INSERT INTO `house_allocation` (`indexNumber`, `schoolID`, `studentLname`, `studentOname`, `houseID`, `studentYearLevel`, `studentGender`, `boardingStatus`) VALUES (?, ?, ?, ?, NULL, 1, ?, ?)";
                    $stmt = $connect->prepare($sql);
                    $stmt->bind_param("sissss",$_GET["indexNumber"], $student["schoolID"], $student["Lastname"], $student["Othernames"], $student["Gender"], $student["boardingStatus"]);
                    $stmt->execute();
                }
                
                //set houses to 0
                $houses = 0;
            }
        }
?>
<head>
    <title>Download Documents</title>
    <script src="<?php echo $url?>/assets/scripts/jquery/uncompressed_jquery.js"></script>
    <meta name="robots" content="noindex, nofollow">
    <style>
        #container{
            width: 100vw;
            height: 100vh;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        a:visited{
            color: blue;
        }
        body, html{
            padding: 0; margin: 0;
        }
        button{
            max-width: 640px;
            width: 70vw;
            height: 10vh;
            padding: 0.3em 0.5em;
            cursor: pointer;
            outline: unset;
            border: 1px solid lightgrey;
            transition: all 0.5s;
            font-size: 13pt;
            background-color: #eee;
            color: #222;
        }
        button:hover{
            background-color: #28a745;
            color: #eee;
        }
        .member_div{
            padding: 0.5em;
        }
    </style>
</head>

<body>
    <?php 
        $prospectus_array = $_SESSION["ad_school_prospectus"];

        if($prospectus_array){
            $prospectus_array = json_decode($prospectus_array, true);

            if($prospectus_array && $prospectus_array["type"] == "single"){
                $prospectus = $prospectus_array["files"];
            }else{
                if(strtolower($_SESSION["ad_stud_residence"]) == "day"){
                    $prospectus = $prospectus_array["files"]["day"];
                }else{
                    $prospectus = $prospectus_array["files"][strtolower($_SESSION["ad_stud_gender"])];
                }
            }
        }
    ?>
    <div id="container">
        <div class="member_div">
            <?php if(!empty($prospectus)): ?>
            <a href="<?php echo $url."/".$prospectus ?>" rel="nofollow">
                <button id="btn_pros">Download School's Prospectus | Download [PDF]</button>
            </a>
            <?php else: ?>
                <a href="javascript:void(0)" rel="nofollow">
                    <button id="btn_pros">No Prospectus provided</button>
                </a>
            <?php endif; ?>
        </div>
        <div class="member_div">
            <a href="<?php echo $url?>/enrolPDF.php" rel="nofollow">
                <button id="btn_ad">My Enrolment Information | Download [PDF]</button>
            </a>
        </div>
        <?php if($houses > 0){?>
        <div class="member_div">
            <a href="<?php echo $url?>/customPdfGenerator.php" rel="nofollow">
                <button id="btn_ad">Admission letter is ready for download | Download [PDF]</button>
            </a>
        </div>
        <?php }else{ ?>
        <div class="member_div">
            <span>
                <button>Sorry, admission letter not ready, please try again at a later time</button>
            </span>
        </div>
        <?php } ?>
        <?php if(!isset($_GET["indexNumber"])){ ?>
        <div class="member_div">
            <span>You can visit <a href="<?php echo "$url/student"?>"><?php echo "$url/student"?></a> to download your documents at a later time.</span>
        </div>
        <?php } ?>
    </div>

    <script>
    </script>
</body>
<?php
    }else{
        echo "No result to deliver";
    }

    close_connections();
?>