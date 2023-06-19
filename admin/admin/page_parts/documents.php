<?php   
    if(isset($_REQUEST["school_id"]) && !empty($_REQUEST["school_id"])){
        $user_school_id = $_REQUEST["school_id"];
        $user_details = getUserDetails($_REQUEST["user_id"]);
        
        include_once("../../includes/session.php");
    }else{
        include_once("../../../includes/session.php");
    
        //set nav_point session
        $_SESSION["nav_point"] = "documents";
    }
?>

<section id="main_menu" class="sp-xlg-tp">
    <p class="txt-al-c">Select the type of document template you want to retrieve</p>
    <div class="btn sm-auto p-lg m-sm">
        <button class="plain-r primary section_btn" data-section="stud_list">Student Lists</button>
        <button class="plain-r primary section_btn" data-section="attendance_list">Attendance List</button>
        <button class="plain-r primary section_btn" data-section="special">Special Documents</button>
    </div>
</section>

<section class="btn_section txt-al-c p-xlg-lr p-xxlg-tp txt-fl stud_list">
    <p>Use this section to retrieve document for the list of your students</p>
</section>
<section class="btn_section stud_list">
    <div class="form" name="student_list">
        <h3 class="txt-al-c">Select Your filters</h3>
        <div class="joint gap-sm">
            <label for="program" class="flex-column gap-sm">
                <span class="label_title">Select Program</span>
                <select name="program" id="program">
                    <?php 
                        $sql = "SELECT DISTINCT(programme) FROM students_table WHERE school_id=$user_school_id";
                        $results = $connect2->query($sql);

                        if($results->num_rows > 0){
                            echo "<option value=\"all\">All Programmes</option>";
                            while($row = $results->fetch_assoc()){
                                echo "<option value=\"{$row['programme']}\">{$row['programme']}</option>";
                            }
                        }else{
                            echo "<option value=\"\">No student uploaded</option>";
                        }
                    ?>
                </select>
            </label>
            <label for="program_year" class="flex-column gap-sm">
                <span class="label_title">Select Year</span>
                <select name="program_year" id="program_year">
                    <?php 
                        $sql = "SELECT DISTINCT(studentYear) FROM students_table WHERE school_id=$user_school_id";
                        $results = $connect2->query($sql);

                        if($results->num_rows > 0){
                            echo "<option value=\"all\">All Years</option>";
                            while($row = $results->fetch_assoc()){
                                echo "<option value=\"{$row['studentYear']}\">{$row['studentYear']}</option>";
                            }
                        }else{
                            echo "<option value=\"\">No student uploaded</option>";
                        }
                    ?>
                </select>
            </label>
            <label for="student_gender" class="flex-column gap-sm">
                <span class="label_title">Select Gender</span>
                <select name="student_gender" id="student_gender">
                    <?php 
                        $sql = "SELECT DISTINCT(Gender) FROM students_table WHERE school_id=$user_school_id";
                        $results = $connect2->query($sql);

                        if($results->num_rows > 0){
                            echo "<option value=\"all\">All Genders</option>";
                            while($row = $results->fetch_assoc()){
                                echo "<option value=\"{$row['Gender']}\">{$row['Gender']}</option>";
                            }
                        }else{
                            echo "<option value=\"\">No student uploaded</option>";
                        }
                    ?>
                </select>
            </label>
        </div>
    </div>
</section>

<section class="btn_section txt-al-c p-xlg-lr p-xxlg-tp txt-fl attendance_list">
    <p>Use this section to retrieve class attendance list templates</p>
</section>
<section class="btn_section attendance_list">
    <div class="form" name="attendance_form">
        <h3 class="txt-al-c">Select your filters</h3>
        <div class="joint gap-sm">
            <label for="program_id" class="flex-column gap-sm">
                <span class="label_title">Select the choice of class</span>
                <select name="program_id" id="program_id">
                    <?php 
                        $programs = fetchData1("program_id, program_name","program","school_id=$user_school_id", 0);

                        if(is_array($programs)){
                            echo "<option value=\"all\">All Classes</option>";

                            if(array_key_exists("program_id",$programs)){
                                $pr = $programs;
                                $programs = null;
                                $programs[0] = $pr;
                            }

                            foreach($programs as $program){
                                echo "<option value=\"{$program['program_id']}\">{$program['program_name']}</option>";
                            }
                        }else{
                            echo "<option value=\"\">No student uploaded</option>";
                        }
                    ?>
                </select>
            </label>
            <label for="a_student_year" class="flex-column gap-sm">
                <span class="label_title">Select the program year</span>
                <select name="student_year" id="a_student_year">
                    <?php 
                        $sql = "SELECT DISTINCT(studentYear) FROM students_table WHERE school_id=$user_school_id";
                        $results = $connect2->query($sql);

                        if($results->num_rows > 0){
                            echo "<option value=\"all\">All Years</option>";
                            while($row = $results->fetch_assoc()){
                                echo "<option value=\"{$row['studentYear']}\">{$row['studentYear']}</option>";
                            }
                        }else{
                            echo "<option value=\"\">No student uploaded</option>";
                        }
                    ?>
                </select>
            </label>
            <label for="a_student_semester" class="flex-column gap-sm">
                <span class="label_title">Select a semester</span>
                <select name="student_semester" id="a_student_semester">
                    <option value="">Select a Semester</option>
                    <option value="1">Semester 1</option>
                    <option value="2">Semester 2</option>
                </select>
            </label>
        </div>
    </div>
</section>


<section class="btn_section txt-al-c p-xlg-lr p-xxlg-tp txt-fl special">
    <p>This section would present other special documents</p>
</section>

<section class="btn_section attendance_list stud_list">
    <div class="btn wmax-sm w-full sm-auto p-lg">
        <button class="primary w-full" name="submit" value="get_document">Get Document</button>
    </div>
</section>

<script>
    $(document).ready(function(){
        $(".btn_section").addClass("no_disp")

        $(".section_btn").click(function(){
            const section_id = $(this).attr("data-section");

            $(".btn_section").addClass("no_disp")
            $("." + section_id).removeClass("no_disp")

            $(".section_btn:not(.plain-r)").addClass("plain-r")
            $(this).removeClass("plain-r")
        })

        $("button[name=submit]").click(function(){
            const formName = $(".section_btn:not(.plain-r)").attr("data-section");
            const form = $(".btn_section."+ formName +" .form")
            let form_data = {}
            
            switch(formName){
                case "stud_list":
                    form_data = {
                        submit: "student_list",
                        program_name: $(form).find("select[name=program]").val(),
                        program_year: $(form).find("select[name=program_year]").val(),
                        gender: $(form).find("select[name=student_gender]").val()
                    }
                    break;
                case "attendance_list":
                    form_data = {
                        submit: "attendance_list",
                        program_id: $(form).find("select[name=program_id]").val(),
                        program_year: $(form).find("select[name=student_year]").val(),
                        semester: $(form).find("select[name=student_semester]").val()
                    }
                    break;
                case "special":
                    break;
            }

            /*$.ajax({
                url:"./admin/excelFile.php",
                data: form_data,
                timeout: 10000,
                beforeSend: function(){
                    alert_box("Getting Document...","secondary")
                },
                success: function(response){
                    alert_box(response, "primary", 10)
                },
                error: function(xhr){
                    if(xhr.statusText == "timeout"){
                        alert_box("Connection was timed out due to slow network. Please check and try again", "danger", 6)
                    }
                }
            })*/
            alert_box("Sorry, documents are not yet set. Try again later", "primary", 8)
        })
    })
</script>