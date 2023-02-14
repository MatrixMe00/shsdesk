<?php include_once("includes/session.php")?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php @include_once($rootPath.'/blocks/generalHead.php')?>

    <!--Document title-->
    <title>SHSDesk - Home Page</title>

    <!--Page Meta data-->
    <meta name="description" content="Your number one platform for online admission in Ghana. 
    This system makes the process easier working your admissions right in your comfort zone">
    <meta name="keywords" content="shs, desk, shsdesk, school, online registration, online, registration, registration in ghana, senior high school,
    senior, high, technical school, technical, secondary, secondary school, student admission, student, admission">

    <!--Stylesheets-->
    <link rel="stylesheet" href="assets/styles/index_page.min.css?v=<?php echo time()?>">
    <link rel="stylesheet" href="assets/styles/admissionForm.min.css?v=<?php echo time()?>">

    <!--Payment and angular script-->
    <script src="https://js.paystack.co/v1/inline.js" defer></script>
    <script src="<?php echo $url?>/assets/scripts/angular/angular.min.js?v=<?php echo time()?>"></script>

    <style>
        #message_us{
            padding: 1em; position: fixed; bottom: 5vh; left: 3vw; 
            border-radius: 10px; font-size: large; font-weight: bold;
        }#video{text-align: center; flex: 7 1 600px; margin: auto 10px}
        #contacts{flex:5 1 200px; text-align: center; padding: 0.3rem 1rem 1rem;
            height: -moz-fit-content; height: fit-content; margin: 0 10px}
        #contacts > *{margin: 0.5rem auto}
        #video .head{padding-top: 10px;padding-bottom: 10px;}
        #video .body{padding-bottom: 10px;}
        #video video{max-width: 640px;}
        #download_register{display: inline-block;}
        label[for=student_index_number]{width: 100%}
        @media screen and (max-width: 480px){#message_us{font-size: normal;}}
    </style>
</head>
<body ng-app="index_application" id="index_main">
    <?php @include_once($rootPath.'/blocks/nav.php')?>
    <main>
        <section id="carousel">
            <div class="block">
                <div class="img_container">
                    <?php
                        $result = $connect->query("SELECT item_img, image_alt
                        FROM pageitemdisplays
                        WHERE active=TRUE AND item_type='carousel' AND item_page='home'");

                        if($result->num_rows > 0){
                            while($row=$result->fetch_assoc()){
                    ?>
                    <img src="<?php echo $url."/".$row['item_img']?>" alt="<?php echo $row['image_alt']?>" loading="lazy">
                    <?php }
                    }else{
                        echo "
                        <img src=\"$url/assets/images/default/thought-catalog-xHaZ5BW9AY0-unsplash.jpg\" alt=\"woman writing\" />";
                    } ?>
                    <div class="shadow"></div>
                </div>
                <div class="prev">
                    <span>&leftarrow;</span>
                </div>
                <div class="description">
                    <?php
                        $result = $connect->query("SELECT item_head, item_desc, item_button, button_text
                        FROM pageitemdisplays
                        WHERE active=TRUE AND item_type='carousel' AND item_page='home'");

                        if($result->num_rows > 0){
                            while($row=$result->fetch_assoc()){
                    ?>
                    <div class="detail">
                        <div class="head">
                            <h1><?php echo $row['item_head']?></h1>
                        </div>
                        <div class="body">
                            <span class="text txt-fl">
                                <?php echo html_entity_decode($row['item_desc'])?>
                            </span>
                            <?php if($row['item_button'] == "1"){?>
                            <div class="btn sml-unset spl-unset">
                                <button type="button" class="sp-med"><?php echo $row['button_text']?></button>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php }
                    }else{
                        echo "
                        <h1 style=\"font-size: xx-large;\">WELCOME TO SHSDESK</h1>
                        <div class=\"body\" style=\"margin-top: 3vh\">
                            <span class=\"text\" style=\"font-size: larger; padding: 2vh 0;\">
                                This is your number one web application to make your Senior High School admission easy and safe
                            </span>
                        </div>
                        ";
                    } ?>
                </div>
                <div class="next">
                    <span>&bkarow;</span>
                </div>
                <div class="footer">
                    <div class="slider_counter">
                        <span class="active"></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        </section>

        <section id="about">
            <?php $course = fetchData("COUNT(DISTINCT programme) as total","cssps","TRUE=TRUE")["total"];
                if($course > 5){
            ?>
            <div class="control">
                <div class="head">
                    <img src="<?php echo $url?>/assets/images/icons/course.svg" alt="courses">
                </div>
                <div class="desc">
                    <div class="figure">
                    <span><?php echo $course ?></span>
                    </div>
                    <div class="text">
                        <span>Courses</span>
                    </div>
                </div>
            </div>
            <?php } 
                $cssps = fetchData("COUNT(indexNumber) as total","cssps","enroled=TRUE")["total"];
                
                if($cssps >= 30){
            ?>
            <div class="control">
                <div class="head">
                    <img src="<?php echo $url?>/assets/images/icons/student.svg" alt="student">
                </div>
                <div class="desc">
                    <div class="figure">
                        <span><?php echo numberShortner($cssps)."<b>+</b>" ?></span>
                    </div>
                    <div class="text">
                        <span>Students Admitted</span>
                    </div>
                </div>
            </div>
            <?php } 
                $system = fetchData("COUNT(indexNumber) as total","cssps","TRUE=TRUE")["total"];
                
                if($system >= 50){
            ?>
            <div class="control">
                <div class="head">
                    <img src="<?php echo $url?>/assets/images/icons/teacher.svg" alt="teacher">
                </div>
                <div class="desc">
                    <div class="figure">
                    <span><?php echo numberShortner($system)."<b>+</b>" ?></span>
                    </div>
                    <div class="text">
                        <span>Students Placed</span>
                    </div>
                </div>
            </div>
            <?php } ?>
            <div class="control">
                <div class="head">
                    <img src="<?php echo $url?>/assets/images/icons/region.svg" alt="region">
                </div>
                <div class="desc">
                    <div class="figure">
                    <span><?php echo fetchData("COUNT(id) as total","schools","TRUE=TRUE")["total"] ?></span>
                    </div>
                    <div class="text">
                        <span>Registered Schools</span>
                    </div>
                </div>
            </div>
            <div class="shadow"></div>
        </section>

        <section id="student" class="flex-all-center flex-column">
            <div class="selection w-fluid w-fluid-child flex flex-space-around flex-wrap">
                <div class="case" data-box="payment_form" id="school_admission_case">
                    <h3>Online SHS Admission</h3><br>
                    <label for="school_select" class="no_disp">
                        <select name="school_select" id="school_select" class="primary">
                            <option value="NULL">Please select your school</option>
                            <?php
                                $res = $connect->query("SELECT id, schoolName FROM schools WHERE Active = TRUE");
        
                                if($res->num_rows > 0){
                                    while($row = $res->fetch_assoc()){
                                        echo "<option value=\"".$row["id"]."\" class=\"secondary\">".$row["schoolName"]."</option>";
                                        //check if school has at least two houses in the system
                                        /*$house_check = fetchData("COUNT(DISTINCT(title)) AS total", "houses", "schoolID=".$row["id"])["total"];
                                        if($house_check >= 1){
                                            //check if there is at least one student uploaded on the system
                                            $students = fetchData("COUNT(indexNumber) AS total", "cssps", "schoolID=".$row["id"])["total"];
                                            if($students > 0){
                                                echo "<option value=\"".$row["id"]."\">".$row["schoolName"]."</option>";
                                            }
                                        }*/
                                    }
                                }
                            ?>
                        </select>
                    </label>
                    <div class="flex flex-align-end" id="pay_div">
                        <label for="student_index_number" class="flex flex-column flex-wrap relative" style="flex: 4">
                            <span class="label_title">Provide your JHS index number below</span>
                            <input type="text" name="student_index_number" id="student_index_number" class="sp-lg" data-index="" placeholder="Enter JHS index number [Eg. 100000000021]">
                        </label>
                        <label for="student_check" class="btn sp-unset w-fluid-child self-align-end" style="flex:1">
                            <button name="student_check" type="button" id="student_check" class="sp-lg-tp primary">Check</button>
                        </label>
                        <label for="payment_button" class="btn sp-unset w-fluid-child self-align-end hide_label no_disp" style="flex: 1">
                            <button name="payment_button" id="payment_button" type="button" class="sp-lg-tp primary">Make My Payment</button>
                        </label>
                    </div>           
                </div>
                <?php
                    if($show){
                ?><div class="case" data-box="results" id="results_case">
                    <h3>End of Semester Results</h3>
                    <label for="school_select2">
                        <select name="school_select2" id="school_select2">
                            <option value="NULL">Please select your school</option>
                            <?php
                                $res = $connect->query("SELECT id, schoolName FROM schools WHERE Active = TRUE");

                                if($res->num_rows > 0){
                                    while($row = $res->fetch_assoc()){
                                        //check if school has at least two houses in the system
                                        $house_check = fetchData("COUNT(DISTINCT(title)) AS total", "houses", "schoolID=".$row["id"])["total"];
                                        if($house_check > 1){
                                            //check if there is at least one student uploaded on the system
                                            $students = fetchData("COUNT(indexNumber) AS total", "cssps", "schoolID=".$row["id"])["total"];
                                            if($students > 0){
                                                echo "<option value=\"".$row["id"]."\">".$row["schoolName"]."</option>";
                                            }
                                        }
                                    }
                                }
                            ?>
                        </select>
                    </label>
                    <label for="year_level" class="hide_label no_disp">
                        <select name="year_level" id="year_level">
                            <option value="NULL">Select Your Year Level</option>
                            <option value="Year1">Year 1</option>
                            <option value="Year2">Year 2</option>
                            <option value="Year3">Year 3</option>
                        </select>
                    </label>
                    <label for="student_name" class="hide_label no_disp">
                        <input type="text" name="student_name" id="student_name" placeholder="Enter your name here" ng-model="student_name" pattern="[a-zA-Z]{4,}" title="Your full name should be 4 characters or more">
                    </label>
                    <label for="search" class="btn hide_label no_disp">
                        <button name="search" id="res_search">Search</button>
                    </label>
                </div><?php } ?>
            </div>
            <label for="student_cancel_operation" class="btn sm-lg-t w-fluid w-fluid-child" style="display:block; text-align: center; width: 100%;">
                    <button name="student_cancel_operation" class="sp-lg-tp secondary" style="max-width: 480px ">Reset</button>
                </label>

            <div id="payment_form" class="form_modal_box no_disp">
                <?php include_once($rootPath."/blocks/admissionPaymentForm.php");?>
            </div>

            <div id="admission" class="form_modal_box flex no_disp">
                <?php include_once($rootPath.'/blocks/admissionForm.php')?>
            </div>

            <?php if($show){ ?><div id="results" class="body hide_label no_disp">
                <table>
                    <thead>
                        <td>ID</td>
                        <td>Exam Type</td>
                        <td>Subject</td>
                        <td>Grade</td>
                        <td>Percent</td>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Exams</td>
                            <td>Mathematics</td>
                            <td>B</td>
                            <td>73</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Exams</td>
                            <td>Social Studies</td>
                            <td>C</td>
                            <td>66</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Exams</td>
                            <td>English</td>
                            <td>A</td>
                            <td>83</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Exams</td>
                            <td>French</td>
                            <td>B</td>
                            <td>77</td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Exams</td>
                            <td>Science</td>
                            <td>C</td>
                            <td>69</td>
                        </tr>
                        <tr>
                            <td>6</td>
                            <td>Exams</td>
                            <td>RME</td>
                            <td>A</td>
                            <td>84</td>
                        </tr>
                        <tr>
                            <td>7</td>
                            <td>Exams</td>
                            <td>Citizenship Education</td>
                            <td>B</td>
                            <td>74</td>
                        </tr>
                    </tbody>
                </table>
            </div><?php }?>
        </section>

        <?php if($show){ ?>
        <section id="usage">
            <div class="head">
                <h2>How to register</h2>
            </div>
            <div class="body">
                <p>This is your admission registration made easy. Please follow the steps below to register into your placed school</p>
                <ol class="flex">
                    <div class="flex flex-column">
                        <li>
                            <div class="step_name">Step 1</div>
                            Select your placed school from the list of schools below
                        </li>
                        <li class="arrow">&RightArrowBar;</li>
                        <li>
                            <div class="step_name">Step 2</div>
                            Click on payment and make your payment. Enter your fullname, phone number and email if you have one
                        </li>
                        <li class="arrow">&RightArrowBar;</li>
                        <li>
                            <div class="step_name">Step 3</div>
                            In the case where you have already paid, but due to technical difficulties were unable to complete your 
                            registration, just provide your transaction reference to continue. Transaction reference is in the format
                            Txxxxxxxxxxxxxxx, where x are numbers. Do well to save or write it down when you make initial payment. This
                            is to help you retake an admission form in the case where you could not upload your details.
                        </li>
                        <li class="arrow">&RightArrowBar;</li>
                        <li>
                            <div class="step_name">Step 4</div>
                            Once payment is done, enter your JHS index number to continue.
                        </li>
                        <li class="arrow">&RightArrowBar;</li>
                    </div>
                    
                    <div class="flex flex-column">
                        <li>
                            <div class="step_name">Step 5</div>
                            A valid index number would allow you to continue, an invalid index number or a wrong index number for a selected
                            school will alert you. When this happens, start from point 1, but use point 3 for payment
                        </li>
                        <li class="arrow">&RightArrowBar;</li>
                        <li>
                            <div class="step_name">Step 6</div>
                            Fill in your details and check if every detail is correct in the step three tab.
                        </li>
                        <li class="arrow">&RightArrowBar;</li>
                        <li>
                            <div class="step_name">Step 7</div>
                            When all desired details are entered, click on submit and wait as you are directed to the prospectus and admission
                            document page. On that page, do well to click on the buttons to download the desired documents
                        </li>
                        <li class="arrow">&RightArrowBar;</li>
                        <li>
                            <div class="step_name">Step 8</div>
                            If you were unable to download your admission letter, head to the <a href="<?php echo $url?>/student/" style="color: blue">students</a>
                            panel to re-download them. Make sure you are fully registered before using this url.
                        </li>
                    </div>                    
                </ol>
            </div>
        </section>
        <?php }?>
        
        <div class="flex flex-wrap flex-center-content">
            <section id="contacts" class="secondary">
                <div class="head">
                    <h3>For Assistance</h3>
                </div>
                <div class="body">
                    <p>Please select your choice school to get the contact of your admin</p>
                    <label for="getContact">
                        <select name="getContact" id="getContact">
                            <option value="">Select A School</option>
                            <?php 
                                $sql = "SELECT id, schoolName FROM schools WHERE Active=1";
                                $res = $connect->query($sql);
                                if($res->num_rows > 0){
                                    while($row = $res->fetch_assoc()){
                                        echo "
                                        <option value=\"".$row['id']."\">".$row["schoolName"]."</option>
                                        ";
                                    }
                                }
                            ?>
                        </select>
                    </label>                    
                    <span id="contResult"></span>
                </div>
            </section>
            <section id="video" class="light">
                <div class="head">
                    <h3>How to Register your details</h3>
                </div>
                <div class="body">
                    <p>Please take a look at the video below to be guided on how to register yourself for admission</p>
                </div>
                <div>
                    <video controls width="80%" cite="https://www.fesliyanstudios.com">
                        <source  src="<?php echo $url?>/assets/file/SHSDesk Demo Video - 720px.mp4" type="video/mp4">
                        Video not supported by browser
                    </video>
                    
                </div>
                <div id="download_register" class="sm-sm-b btn m-sm">
                    <a href="<?php echo $url?>/assets/file/SHSDesk Demo Video - 720px.mp4" download>
                        <button type="button" class="light border b-secondary plain-r">Download Video [720px]</button>
                    </a>
                    <a href="<?php echo $url?>/assets/file/SHSDesk Demo Video - 1080px.mp4" download>
                        <button type="button" class="light border b-secondary plain-r">Download Video [1080px]</button>
                    </a>
                </div>
                
            </section>
        </div>
        
    </main>
    
    <a href="https://wa.me/233200449223">
        <span id="message_us" class="primary">
            Message Us
        </span>
    </a>

    <?php @include_once($rootPath.'/blocks/footer.php')?>

    <!--Document scripts-->
    <script src="assets/scripts/form/general.min.js?v=<?php echo time()?>"></script>
    <script src="assets/scripts/index.min.js?v=<?php echo time()?>"></script>
    <script src="assets/scripts/head_foot.min.js?v=<?php echo time()?>"></script>
    <script src="assets/scripts/admissionForm.min.js?v=<?php echo time(); ?>"></script>

    <!--Angular scripts-->
    <script src="assets/scripts/angular_index.js?v=<?php echo time()?>"></script>

    <!--Payment scripts-->
    <script src="assets/scripts/form/payForm.min.js?v=<?php echo time();?>"></script>
</body>
</html>