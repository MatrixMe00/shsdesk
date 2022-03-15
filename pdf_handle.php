<?php
    require_once("includes/session.php");

    //provide links for admission letter and prospectus
    if((isset($_SESSION["ad_stud_index"]) && !empty($_SESSION["ad_stud_index"])) || isset($_GET["indexNumber"])){

        //if user enters via url or already enroled
        if(isset($_GET["indexNumber"]) && !empty($_GET["indexNumber"])){
            //create required session objects
            $student = fetchData("c.*, e.enrolCode","cssps c JOIN enrol_table e ON c.indexNumber = e.indexNumber", "c.indexNumber='".$_GET["indexNumber"]."'");
            $school = getSchoolDetail($student["schoolID"], true);

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
            $_SESSION["ad_reopening"] = fetchData("reopeningDate","admissiondetails","schoolID=".$student["schoolID"])["reopeningDate"];

            //student details
            $_SESSION["ad_stud_index"] = $student["indexNumber"];
            $_SESSION["ad_stud_lname"] = $student["Lastname"];
            $_SESSION["ad_stud_oname"] = $student["Othernames"];
            $_SESSION["ad_stud_enrol_code"] = $student["enrolCode"];
            $_SESSION["ad_stud_residence"] = $student["boardingStatus"];
            $_SESSION["ad_stud_program"] = $student["programme"];
            $_SESSION["ad_stud_gender"] = $student["Gender"];
            $_SESSION["ad_stud_house"] = fetchData("title","houses","id=".fetchData("houseID","house_allocation","indexNumber='".$student["indexNumber"]."'")["houseID"])["title"];
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
        body{
            overflow: hidden;
        }
        button{
            width: 70vw;
            height: 10vh;
            padding: 0.3em 0.5em;
            cursor: pointer;
            outline: unset;
            border: 1px solid lightgrey;
            transition: all 0.6s;
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
    <div id="container">
        <div class="member_div">
            <a href="<?php echo $url."/".$_SESSION["ad_school_prospectus"] ?>" rel="nofollow">
                <button id="btn_pros">Prospectus is ready for download | Download [PDF]</button>
            </a>
        </div>
        <div class="member_div">
            <a href="<?php echo $url?>/customPdfGenerator.php" rel="nofollow">
                <button id="btn_ad">Admission letter is ready for download | Download [PDF]</button>
            </a>
        </div>
    </div>

    <script>
    </script>
</body>
<?php
    }else{
        echo "No result to deliver";
    }
?>