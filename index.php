<?php include_once("includes/session.php")?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php @include_once($rootPath.'/blocks/generalHead.php')?>

    <!--Document title-->
    <title>SHSDESK</title>

    <!--Meta data-->
    <meta name="description" content="">

    <!--Stylesheets-->
    <link rel="stylesheet" href="assets/styles/index_page.css?v=<?php echo time()?>">
    <link rel="stylesheet" href="assets/styles/admissionForm.css?v=<?php echo time()?>">

    <!--Payment script-->
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="assets/scripts/form/paystack.js"></script>
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
                    <img src="<?php echo $url."/".$row['item_img']?>" alt="<?php echo $row['image_alt']?>">
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
                            <span class="text">
                                <?php echo $row['item_desc']?>
                            </span>
                            <?php if($row['item_button'] == "1"){?>
                            <div class="btn">
                                <button type="button"><?php echo $row['button_text']?></button>
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
                                This is your number one web application to make your admission easy and safe
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
            <div class="control">
                <div class="head">
                    <img src="<?php echo $url?>/assets/images/icons/course.svg" alt="courses">
                </div>
                <div class="desc">
                    <div class="figure">
                        <span>250</span>
                    </div>
                    <div class="text">
                        <span>Courses</span>
                    </div>
                </div>
            </div>
            <div class="control">
                <div class="head">
                    <img src="<?php echo $url?>/assets/images/icons/student.svg" alt="student">
                </div>
                <div class="desc">
                    <div class="figure">
                        <span>11,254</span>
                    </div>
                    <div class="text">
                        <span>Students Admitted</span>
                    </div>
                </div>
            </div>
            <div class="control">
                <div class="head">
                    <img src="<?php echo $url?>/assets/images/icons/teacher.svg" alt="teacher">
                </div>
                <div class="desc">
                    <div class="figure">
                        <span>360</span>
                    </div>
                    <div class="text">
                        <span>Teachers</span>
                    </div>
                </div>
            </div>
            <div class="control">
                <div class="head">
                    <img src="<?php echo $url?>/assets/images/icons/region.svg" alt="region">
                </div>
                <div class="desc">
                    <div class="figure">
                        <span>16</span>
                    </div>
                    <div class="text">
                        <span>Regions</span>
                    </div>
                </div>
            </div>
            <div class="shadow"></div>
        </section>

        <section id="student">
            <div class="selection flex flex-space-around flex-wrap">
                <div class="case" data-box="payment_form" id="school_admission_case">
                    <h3>Online SHS Admission</h3>
                    <label for="school_select">
                        <select name="school_select" id="school_select">
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
                    <label for="payment_button" class="btn hide_label no_disp">
                        <button name="payment_button" id="payment_button" type="button">Make My Payment</button>
                    </label>
                </div>
                <div class="case" data-box="results" id="results_case">
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
                </div>
            </div>
            <label for="student_cancel_operation" class="btn" style="display:block; text-align: center; width: 100%;">
                    <button name="student_cancel_operation">Reset</button>
                </label>

            <div id="payment_form" class="form_modal_box no_disp">
                <?php include_once($rootPath."/blocks/admissionPaymentForm.php");?>
            </div>

            <div id="admission" class="form_modal_box flex no_disp">
                <?php include_once($rootPath.'/blocks/admissionForm.php')?>
            </div>

            <div id="results" class="body hide_label no_disp">
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
            </div>
        </section>
    </main>

    <!--<iframe src="https://calendar.google.com/calendar/embed?src=ird1d5tafplcn4ccght0ihc9ng%40group.calendar.google.com&ctz=Africa%2FAccra" style="border: 0" width="800" height="600" frameborder="0" scrolling="no"></iframe>-->

    <?php @include_once($rootPath.'/blocks/footer.php')?>    

    <!--Document scripts-->
    <script src="assets/scripts/form/general.js?v=<?php echo time()?>"></script>
    <script src="assets/scripts/index.js?v=<?php echo time()?>"></script>
    <script src="assets/scripts/head_foot.js?v=<?php echo time()?>"></script>
    <script src="assets/scripts/admissionForm.js?v=<?php echo time(); ?>"></script>

    <!--Angular scripts-->
    <script src="assets/scripts/angular_index.js?v=<?php echo time()?>"></script>

    <!--Payment scripts-->
    <script src="assets/scripts/form/payForm.js?v=<?php echo time();?>"></script>
</body>
</html>